<?php

namespace CanvasApiLibrary\Caching\AccessAware\Interfaces;

use CanvasApiLibrary\Caching\AccessAware\CacheResult;

interface CacheStorageInterface{
    /**
     * Sets a single item in the cache, individually.
     * @param string $key
     * @param mixed $value
     * @param int $ttl Times in seconds to keep cache
     * @param string $permissionsRequired Required permission token to access this cache. 
     *      User has to provide this tokens in order to allow for cache hit.
     * @param string $clientID The ID by which to identify this user in the system. 
     *      Items added automatically have the associated permissions added to the clients permissions list.
     * @return void
     */
    public function setSingleItem(string $key, mixed $value, int $ttl, string $permissionRequired, string $clientID);
    
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
    public function setCollectionItem(string $collectionKey, string $itemKey, mixed $value, int $ttl, string $itemPermissionRequired, string $clientID);

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
    public function setCollection(string $collectionKey, array $itemKeys, int $ttl, string $clientID);

    /**
     * Tries to retrieve a regular singular value from the cache.
     * @param string $key They key by which to find the item.
     * @param string $clientID The ID by which to identify this user in the system.
     * @return CacheResult
     */
    public function getSingleItem(string $key, string $clientID): CacheResult;

    /**
     * Tries to retrieve a cached collections of items.
     * @param string $collectionKey The key by which the collection is cached.
     * @param string $clientID The ID by which to identify this user in the system.
     * @return CacheResult
     */
    public function getCollection(string $collectionKey, string $clientID): CacheResult;

    /**
     * Returns a boolean indicating whether or not the client has any know permissions in a given context.
     * @param string $clientID
     * @param string $examplePermission A permission from the context in which you wish to check for existing permissions.
     * @return bool
     */
    public function hasPermissionsInContext(string $clientID, string $examplePermission): bool;
}