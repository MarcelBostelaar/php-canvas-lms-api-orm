<?php

namespace CanvasApiLibrary\Caching\AccessAware\Interfaces;

use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;

/**
 * Caching class used in cached providers.
 * @template Permission
 * @template ContextFilter
 * @template PermissionType
 */
class CacheStorage{
    /**
     * @param CacheProvider<Permission, ContextFilter, PermissionType> $cache
     * @param UserProviderInterface $userProvider
     * @param PermissionsHandlerInterface<Permission, ContextFilter, PermissionType> $permissionHandler
     */
    public function __construct(
        private readonly CacheProvider $cache,
        private readonly UserProviderInterface $userProvider,
        private readonly PermissionsHandlerInterface $permissionHandler
        ) {
    }

    /**
     * Returns a boolean indicating whether or not the client has any know permissions in a given context.
     * @param string $clientID
     * @param ContextFilter $contextFilter Filter for the context in which to check for any existing permissions.
     * @return bool
     */
    protected function hasPermissionsInContext(string $clientID, mixed $contextFilter): bool{
        return $this->cache->hasPermissionsInContext($clientID, $contextFilter);
    }

    /**
     * Sets a single item in the cache, individually.
     * @param string $key
     * @param mixed $value
     * @param int $ttl Times in seconds to keep cache
     * @param ContextFilter $permissionsRequired Permission tokens to access this cache. 
     *      User has to provide this tokens in order to allow for cache hit. If client has any of the permissions, can access item.
     * @param string $clientID The ID by which to identify this user in the system. 
     *      Items added automatically have the associated permissions added to the clients permissions list.
     * @return void
     */
    public function setSingleItem(string $key, mixed $value, int $ttl, 
    mixed $permissionRequired, string $clientID){
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
     * @param Permission $itemPermissionRequired Required permission token to access this individual item. 
     *      User has to provide this tokens in order to allow for cache hit. 
     * @param string $clientID The ID by which to identify this user in the system.
     *      Items added automatically have the associated permissions added to the clients permissions list.
     * @return void
     */
    public function setCollectionItem(string $collectionKey, string $itemKey, mixed $value, int $ttl, 
    mixed $itemPermissionRequired, string $clientID){
        $this->cache->set($itemKey, $value, $ttl, $itemPermissionRequired);
        $this->cache->addPermission($clientID, $itemPermissionRequired, $ttl);
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
        $this->cache->setCollectionSet($clientID, $collectionKey, $itemKeys, $ttl);
    }

    /**
     * Creates a new collection (if it does not already exist) endpoint. Must be called before saving items to this collection.
     * @param string $collectionKey Key
     * @param ?ContextFilter $collectionItemPersmissionContext Context filter for the items in this collection. Leave empty if unknown.
     * @param int $ttl Time to keep this collection in cache, in seconds.
     * @return void
     */
    public function ensureCollection(string $collectionKey, mixed $collectionItemPersmissionContext, int $ttl){
        $this->cache->ensureCollection($collectionKey, $collectionItemPersmissionContext, $ttl);
    }

    /**
     * Summary of setBackpropagation
     * @param string $collectionKey
     * @param PermissionType $permissionType
     * @param string $target
     * @return void
     */
    public function setBackpropagation(string $collectionKey, mixed $permissionType, string $target){
        $this->cache->setBackpropagation($collectionKey, $permissionType, $target);
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
        
        $requiredPermissions = $this->cache->getCollectionItemPermissionsRequired($collectionKey);
        $filter = $this->cache->getCollectionPermissionContext($collectionKey);
        if($filter == null){
            //Cant perform special check below
            return new CacheResult(null, false);
        }

        $clientPermissions = $this->permissionHandler::filterOnContext($filter, $this->cache->getClientPermissions($clientID));

        //if the required permissions of all known items in this collection 
        // is a superset of the required permissions (for this context) that the client has, 
        // then the request can be fullfilled with cached data. 
        // (ie for each domain-course-user requirement there is an item that carries the permissions, 
        // so all normally returned items for this user will be returned. 
        // We assume the user already has maximum permissions in this context.)
        //client without permission is valid case, just returns empty results, they arent allowed to see anything.
        if(self::is_superset($requiredPermissions, $clientPermissions)){
            //return a filtered list of all cached items
            return $this->cache->getCollectionItems($collectionKey, $clientPermissions, $clientID);
        }

        //Cannot ensure the stored items are everything the client would normally get, 
        // as they have permissions for data in this context that has no corresponding item stored.
        return new CacheResult(null, false);
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
            $this->hasPermissionsInContext($clientID, $this->permissionHandler::contextFilterDomainCourse($course)) ||
            $this->hasPermissionsInContext($clientID, $this->permissionHandler::contextFilterDomainCourseUser($course))){
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

    /**
     * Summary of trySingleValue
     * @param string $itemKey
     * @param int $ttl
     * @param string $clientID
     * @param Permission $permission
     * @param callable $wrappedFunc
     * @return mixed
     */
    public function trySingleValue(string $itemKey, int $ttl, string $clientID, mixed $permission, callable $wrappedFunc): mixed{
        $found = $this->getSingleItem($itemKey, $clientID);
        if($found->hit){
            return $found->value;
        }
        $actualValue = $wrappedFunc();
        $this->setSingleItem($itemKey, $actualValue, $ttl, $permission, $clientID);
        return $actualValue;
    }

    /**
     * Summary of ensureThenTrySingleValue
     * @param string $itemKey
     * @param int $ttl
     * @param Course $course
     * @param string $clientID
     * @param Permission $permission
     * @param callable $wrappedFunc
     * @return mixed
     */
    public function ensureThenTrySingleValue(string $itemKey, int $ttl, Course $course, string $clientID, mixed $permission, callable $wrappedFunc): mixed{
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