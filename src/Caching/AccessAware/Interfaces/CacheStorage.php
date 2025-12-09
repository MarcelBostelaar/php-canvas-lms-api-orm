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
     * @param CacheProviderInterface<Permission, ContextFilter, PermissionType> $cache
     * @param UserProviderInterface $userProvider
     * @param PermissionsHandlerInterface<Permission, ContextFilter, PermissionType> $permissionHandler
     */
    public function __construct(
        private readonly CacheProviderInterface $cache,
        private readonly UserProviderInterface $userProvider,
        private readonly PermissionsHandlerInterface $permissionHandler
        ) {
    }

    /**
     * Sets a single item in the cache, individually.
     * @param string $key
     * @param mixed $value
     * @param int $ttl Times in seconds to keep cache
     * @param string $clientID The ID by which to identify this user in the system. 
     *      Items added automatically have the associated permissions added to the clients permissions list.
     * @param ContextFilter[] $permissionsRequired Permission tokens to access this cache. 
     *      User has to provide this tokens in order to allow for cache hit. If client has any of the permissions, can access item.
     * @return void
     */
    public function setSingleItem(string $key, mixed $value, int $ttl, string $clientID, 
    mixed ...$permissionRequired){
        $this->cache->set($key, $value, $ttl, $clientID, $permissionRequired);
    }

    // /**
    //  * Sets an item as part of a collection in the cache.
    //  * Only use this if the endpoint being cached returns the same data to everyone, with the only filter being automatic role-based access permission.
    //  * All items in a collection are collected in one whole, unique and last added using the itemKey as individual item unique id.
    //  * Retrieving a collection filters the cached collection based on permissions.
    //  * Use this method by adding all individual items one by one.
    //  * 
    //  * Using this method alone is not enough to guarantee a cache hit for collections,
    //  * as all known permissions of the client retrieving will be checked to have 
    //  * at least one corresponding item in the known cached results for this collection.
    //  * Any method cached via this method that inherently lacks an item (such as a student who did not submit any items)
    //  * will lead to a cache miss, as the full result cannot be guaranteed.
    //  * To resolve this, be sure to also store your collection result using {@see CacheStorageInterface::setCollection}
    //  * 
    //  * @param string $collectionKey The key of the collection in the cache it belongs to.
    //  * @param string $itemKey The key that uniquely identifies the individual item.
    //  * @param mixed $value
    //  * @param int $ttl Times in seconds to keep cache
    //  * @param string $clientID The ID by which to identify this user in the system.
    //  *      Items added automatically have the associated permissions added to the clients permissions list.
    //  * @param Permission[] $itemPermissionsRequired Required permission token to access this individual item. 
    //  *      User has to provide any of these tokens in order to allow for cache hit. 
    //  * @return void
    //  */
    // public function setCollectionItem(string $collectionKey, string $itemKey, mixed $value, int $ttl, string $clientID,
    // mixed ...$itemPermissionsRequired){
    //     $this->cache->set($itemKey, $value, $ttl, $itemPermissionsRequired);
    //     $this->cache->addPermissions($clientID, $ttl, $itemPermissionsRequired);
    //     $this->cache->addToCollection($collectionKey, $itemKey, $ttl);
    // }
    
    // /**
    //  * Save the retrieved set of items (saved by itemKey) for this client.
    //  * This is a secondary method, must always be used in conjunction with {@see CacheStorageInterface::setCollectionItem}.
    //  * Will not add any permissions to the client, 
    //  * as the items need to be saved individually and this already sets their permissions.
    //  * @param string $collectionKey The key of the collection in the cache to retrieve.
    //  * @param array $itemKeys The cache keys for the retrieved items
    //  * @param int $ttl Times in seconds to keep cache
    //  * @param string $clientID The ID by which to identify this user in the system.
    //  * @return void
    //  */
    // public function setCollection(string $collectionKey, array $itemKeys, int $ttl, 
    // string $clientID){
    //     $this->cache->setCollectionSet($clientID, $collectionKey, $itemKeys, $ttl);
    // }

    // /**
    //  * Creates a new collection (if it does not already exist) endpoint. Must be called before saving items to this collection.
    //  * @param string $collectionKey Key
    //  * @param int $ttl Time to keep this collection in cache, in seconds.
    //  * @param ContextFilter[] $collectionItemPersmissionContexts Allowed context filters for this collectio. Any collection may only have ONE filter of any TYPE. 
    //  * Adding a filter of the same type of a different context throws an exception.
    //  * @return void
    //  */
    // public function ensureCollection(string $collectionKey, int $ttl, array $collectionItemPersmissionContext){
    //     $this->cache->ensureCollection($collectionKey, $ttl, $collectionItemPersmissionContext);
    // }

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
        return $this->cache->getCollection($clientID, $collectionKey);
    }

    //Utility
    /**
     * Summary of trySingleValue
     * @param string $itemKey
     * @param int $ttl
     * @param string $clientID
     * @param callable $wrappedFunc Closure that generates the actual item if called.
     * @param Permission[] $permissions Permissions to add to item if set in cache anew.
     * @return mixed The retrieved or created item.
     */
    public function trySingleValue(string $itemKey, int $ttl, string $clientID, callable $wrappedFunc, mixed ...$permissions): mixed{
        $found = $this->getSingleItem($itemKey, $clientID);
        if($found->hit){
            return $found->value;
        }
        $actualValue = $wrappedFunc();
        $this->setSingleItem($itemKey, $actualValue, $ttl, $clientID, $permissions);
        return $actualValue;
    }
}