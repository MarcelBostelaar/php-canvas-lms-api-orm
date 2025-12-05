<?php

namespace CanvasApiLibrary\Caching\AccessAware\Interfaces;

use CanvasApiLibrary\Caching\AccessAware\CacheResult;
use CanvasApiLibrary\Caching\AccessAware\PermissionsHandler;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;

class CacheStorage{

    public function __construct(
        private readonly CacheProvider $cache,
        private readonly UserProviderInterface $userProvider
        ) {
    }

    /**
     * Returns a boolean indicating whether or not the client has any know permissions in a given context.
     * @param string $clientID
     * @param string $contextFilter Filter for the context in which to check for any existing permissions.
     * @return bool
     */
    protected function hasPermissionsInContext(string $clientID, string $contextFilter): bool{
        return $this->cache->hasPermissionsInContext($clientID, $contextFilter);
    }

    /**
     * Sets a single item in the cache, individually.
     * @param string $key
     * @param mixed $value
     * @param int $ttl Times in seconds to keep cache
     * @param string $permissionsRequired Permission tokens to access this cache. 
     *      User has to provide this tokens in order to allow for cache hit. If client has any of the permissions, can access item.
     * @param string $clientID The ID by which to identify this user in the system. 
     *      Items added automatically have the associated permissions added to the clients permissions list.
     * @return void
     */
    public function setSingleItem(string $key, mixed $value, int $ttl, 
    string $permissionRequired, string $clientID){//TODO put permissions at the end, make it ...args, so you can add an arbitrary amount of permissions.
        $this->cache->set($key, $value, $ttl, $permissionRequired);
        $this->cache->addPermission($clientID, $permissionRequired, $ttl);
    }

    /**
     * Sets an item as part of a collection in the cache.
     * Only use this if the endpoint being cached returns the same data to everyone, with the only filter being automatic role-based access permission.
     * All items in a collection are collected in one whole, unique and last added using the itemKey as individual item unique id.
     * Retrieving a collection filters the cached collection based on permissions.
     * Use this method by adding all individual items one by one.
     * 
     * Using this method alone is not enough to guarantee a cache hit for collections,
     * as all known permissions of the client retrieving will be checked to have 
     * at least one corresponding item in the known cached results for this collection.
     * Any method cached via this method that inherently lacks an item (such as a student who did not submit any items)
     * will lead to a cache miss, as the full result cannot be guaranteed.
     * To resolve this, be sure to also store your collection result using {@see CacheStorageInterface::setCollection}
     * 
     * @param string $collectionKey The key of the collection in the cache it belongs to.
     * @param string $itemKey The key that uniquely identifies the individual item.
     * @param mixed $value
     * @param int $ttl Times in seconds to keep cache
     * @param string $itemPermissionRequired Required permission token to access this individual item. 
     *      User has to provide this tokens in order to allow for cache hit. 
     * @param string $clientID The ID by which to identify this user in the system.
     *      Items added automatically have the associated permissions added to the clients permissions list.
     * @return void
     */
    public function setCollectionItem(string $collectionKey, string $itemKey, mixed $value, int $ttl, 
    string $itemPermissionRequired, string $clientID){//TODO put permissions at end, allow many
        $this->cache->set($itemKey, $value, $ttl, $itemPermissionRequired);
        $this->cache->addPermission($clientID, $itemPermissionRequired, $ttl);
        $this->cache->ensureCollection($collectionKey, PermissionsHandler::contextFrom($itemPermissionRequired), $ttl);
        $this->cache->addToCollection($collectionKey, $itemKey, $ttl);
    }
    
    /**
     * Save the retrieved set of items (saved by itemKey) for this client.
     * This is a secondary method, must always be used in conjunction with {@see CacheStorageInterface::setCollectionItem}.
     * Will not add any permissions to the client, 
     * as the items need to be saved individually and this already sets their permissions.
     * @param string $collectionKey The key of the collection in the cache to retrieve.
     * @param array $itemKeys The cache keys for the retrieved items
     * @param int $ttl Times in seconds to keep cache
     * @param string $clientID The ID by which to identify this user in the system.
     * @return void
     */
    public function setCollection(string $collectionKey, array $itemKeys, int $ttl, 
    string $clientID){
        $this->cache->ensureCollection($collectionKey, null, $ttl);
        $this->cache->setCollectionSet($clientID, $collectionKey, $itemKeys, $ttl);
    }
    
    /**
     * Tries to retrieve a regular singular value from the cache.
     * @param string $key They key by which to find the item.
     * @param string $clientID The ID by which to identify this user in the system.
     * @return CacheResult
     */
    public function getSingleItem(string $key, string $clientID): CacheResult{
        return $this->cache->get($clientID, $key);
    }
    
    /**
     * Tries to retrieve a cached collections of items.
     * @param string $collectionKey The key by which the collection is cached.
     * @param string $clientID The ID by which to identify this user in the system.
     * @return CacheResult
     */
    public function getCollection(string $collectionKey, string $clientID): CacheResult{
        $setResult = $this->cache->getCollectionSet($clientID, $collectionKey);
        if($setResult->hit){
            return $setResult;
        }
        
        //array<string,bool>
        $requiredPermissions = $this->cache->getCollectionItemPermissionsRequired($collectionKey);
        $filter = $this->cache->getCollectionPermissionFilter($collectionKey);
        $clientPermissions = $filter !== null ? 
            PermissionsHandler::filterOnContext($filter, $this->cache->getClientPermissions($clientID)) :
            [];

        //if the required permissions of all known items in this collection 
        // is a superset of the required permissions (for this context) that the client has, 
        // then the request can be fullfilled with cached data.
        //client without permission is valid case, just returns empty results.
        if(self::is_superset($requiredPermissions, $clientPermissions)){
            //return a filtered list of all cached items
            return $this->cache->getCollectionItems($collectionKey, $clientPermissions, $clientID);
        }

        //Cannot ensure the stored items are everything the client would normally get, 
        // as they have permissions for data in this context that has no corresponding item stored.
        return new CacheResult(null, false, true);
    }

    //Methods to ensure users get all permissions first.

    /**
     * Ensures that a new client has the maximum amount of permissions from the start, 
     * by fetching all the users in the course that is being operated on, if no permissions are found.
     * @param Course $course
     * @param string $clientID
     * @return void
     */
    public function ensurePermissions(Course $course, string $clientID){
        if(
            $this->hasPermissionsInContext($clientID, PermissionsHandler::contextFilterCoursebound($course)) ||
            $this->hasPermissionsInContext($clientID, PermissionsHandler::contextFilterUserbound($course))){
            return;
        }
        //call saved, cached, user provider. 
        //This ensures that client gets permissions for this course, as well as all user permissions they can.
        $this->userProvider->getUsersInCourse($course, "student");
    }

    //Utility
    /**
     * Combines the performing of setCollectionItem and setCollection using extractors.
     * @param string $collectionKey Key by which the collection is identified in the cache.
     * @param int $ttl Time to keep item in cache in seconds
     * @param array $items Array of items to add to collection
     * @param callable $itemKeyExtractor Closure to extract the item key from this item
     * @param callable $itemPermissionExtractor Closure to extract the permissions needed to access this item
     * @param string $clientID Id by which to identify this client to the cache system.
     * @return void
     */
    public function setCollectionEasy(string $collectionKey, int $ttl, 
    array $items, callable $itemKeyExtractor, callable $itemPermissionExtractor, string $clientID){
        $itemKeys = [];
        foreach ($items as $item) {
            $itemKey = $itemKeyExtractor($item);
            $itemPermission = $itemPermissionExtractor($item);

            $itemKeys[] = $itemKey;

            $this->setCollectionItem($collectionKey, $itemKey, $item, $ttl, 
            $itemPermission, $clientID);
        }
        $this->setCollection($collectionKey, $itemKeys, $ttl, $clientID);
    }

    public function trySingleValue(string $itemKey, int $ttl, string $clientID, string $permission, callable $wrappedFunc): mixed{
        $found = $this->getSingleItem($itemKey, $clientID);
        if($found->hit){
            return $found->value;
        }
        $actualValue = $wrappedFunc();
        $this->setSingleItem($itemKey, $actualValue, $ttl, $permission, $clientID);
        return $actualValue;
    }

    public function ensureThenTrySingleValue(string $itemKey, int $ttl, Course $course, string $clientID, string $permission, callable $wrappedFunc): mixed{
        $this->ensurePermissions($course, $clientID);
        $found = $this->getSingleItem($itemKey, $clientID);
        if($found->hit){
            return $found->value;
        }
        $actualValue = $wrappedFunc();
        $this->setSingleItem($itemKey, $actualValue, $ttl, $permission, $clientID);
        return $actualValue;
    }


    //Real utility

    /**
     * Checks if the first argument is a superset of the second argument.
     * @param string[] $superset
     * @param string[] $subset
     * @return bool
     */
    private static function is_superset(array $superset, array $subset):bool{
        foreach ($subset as $item) {
            if (!\in_array($item, $superset)) {
                return false;
            }
        }
        return true;
    }
}