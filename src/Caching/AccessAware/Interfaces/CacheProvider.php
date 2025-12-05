<?php

namespace CanvasApiLibrary\Caching\AccessAware\Interfaces;

use CanvasApiLibrary\Caching\AccessAware\CacheResult;
use CanvasApiLibrary\Caching\AccessAware\PermissionsHandler;
use LogicException;

/**
 * Abstract class that can be subclassed to make a concrete cache provider for the caching system, with a backing system of your choice.
 */
abstract class CacheProvider{
    /**
     * Sets the value of an item in the cache
     * Permission bound cache operation.
     * @param string $itemKey Cache key for the item, must uniquely identify this item as an individual resource.
     * @param mixed $value Value to cache
     * @param int $ttl Time to keep in cache in seconds.
     * @param string $permissionRequired String indicating the permission required to access this item. Items may have multiple permissions, store all of them, only one is required to access it.
     * @return void
     */
    abstract public function set(string $itemKey, mixed $value, int $ttl, string $permissionRequired);

    /**
     * Adds a permission to a client. Clients can have many permissions.
     * Clientbound cache operation.
     * @param string $clientID The id for the client.
     * @param string $permission The permission to store.
     * @param int $ttl Time to keep in cache in seconds.
     * @return void
     */
    abstract public function addPermission(string $clientID, string $permission, int $ttl);

    /**
     * Creates a new empty collection in the cache if it does not exist already. Otherwise does nothing.
     * Unbound cache operation.
     * @param string $key Key by which to identify the collection.
     * @param ?string $collectionItemPermission A permission for an item in the collection. 
     *  Saved to act as a context filter in other operations. If null, will not be saved.
     * @param mixed $ttl Time to keep in cache in seconds.
     * @return void
     */
    abstract public function ensureCollection(string $key, ?string $collectionItemPermission, int $ttl);

    /**
     * Saves a set of item keys for a given client and a given collection key.
     * Used to get a cache hit on requesting a list of resources you already retrieved.
     * Client bound cache operation.
     * @param string $clientID The ID of this client.
     * @param string $collectionKey The key by which the collection is to be stored. 
     *  Must be identical to the key used to assign items to collections.
     * @param array $itemKeys The list of item keys which belong to this collection.
     * @param int $ttl Time to keep in cache in seconds.
     * @return void
     */
    abstract public function setCollectionSet(string $clientID, string $collectionKey, array $itemKeys, int $ttl);

    /**
     * Adds an item key to the a collection.
     * Unbound cache operation.
     * @param string $collectionKey
     * @param string $itemKey
     * @param int $ttl Time to keep in cache in seconds.
     * @return void
     */
    abstract public function addToCollection(string $collectionKey, string $itemKey, int $ttl);

    /**
     * Tries to retrieve a value by key from the cache. Will do so if the client has any matching permission for any of the permissions of this item.
     * Permission bound cache operation.
     * @param string $clientID Id by which to identify this client.
     * @param string $key Key in the cache
     * @return CacheResult
     */
    abstract public function get(string $clientID, string $key) : CacheResult;

    /**
     * Tries to retrieve a cached collection set of items for this client. If successfull, returns an array of the actual items.
     * Client bound cache operation.
     * @param string $clientID Id by which to identify this client.
     * @param string $collectionKey Key of the collection.
     * @return CacheResult Hit with an array of the actual cached data of the items cached if found. 
     * Miss (empty) otherwise.
     */
    abstract public function getCollectionSet(string $clientID, string $collectionKey): CacheResult;

    /**
     * Returns an array of all permissions needed to access the items stored in this collection, 
     * filtered to only those within the context of the collection.
     * @param string $key Cache key of the collection.
     * @return string[] List of permissions needed to successfully retrieve the full collection.
     */
    public function getCollectionItemPermissionsRequired(string $key): array{
        $allPermissions = $this->getCollectionItemAllPermissionsRequired($key);
        $filter = $this->getCollectionPermissionFilter($key);
        if($filter !== null){
            return PermissionsHandler::filterOnContext($filter, $allPermissions);
        }
        if(count($allPermissions) > 0){
            throw new LogicException("Caching system bug: Cached collection has items, 
            but no context filter is set. Either the context filter is incorrectly saved, 
            incorrectly retrieved, or the CanvasAPILibrary Caching has a programming error.");
        }
        return [];
    }
    
    /**
     * Returns an array of absolutely all permissions needed to access the items stored in this collection.
     * @param string $key Cache key of the collection.
     * @return string[] List of total permissions for all items in this collection.
     */
    abstract protected function getCollectionItemAllPermissionsRequired(string $key): array;

    /**
     * Returns a boolean indicating whether or not the client has any permissions in the given context.
     * @param string $clientID The client ID
     * @param string $examplePermission A permission from the context to check for client permissions.
     * @return bool True if any permission was found. False is no permission found.
     */
    public function hasPermissionsInContext(string $clientID, string $examplePermission): bool{
        $permissions = $this->getClientPermissions($clientID);
        return \count(PermissionsHandler::filterOnContext($examplePermission, $permissions)) > 0;
    }

    /**
     * Returns the permission that was saved with the collection as context filter.
     * @param string $key The collection key.
     * @return ?string The permission that was saved with the collection as a filter. Null is no filter was ever supplied.
     */
    abstract public function getCollectionPermissionFilter(string $key): ?string;

    /**
     * Returns a list of all permissions that this client has.
     * @param string $clientID Id by which to identify this client.
     * @return string[] List of all known permissions, for all contexts.
     */
    abstract public function getClientPermissions(string $clientID): array;

    
    /**
     * Gets all the cached items in the given collection that the provided permissions provide access to.
     * Assumes permissions have already been filtered correctly.
     * @param string $key The collection key.
     * @param string[] $permissions A list of permissions for this request.
     * @param ?string $clientID The ID of the client for which this request has been made. 
     * The retrieved result is also added as a collectionSet for this client.
     * If null, set is not added to client.
     * @return CacheResult The retrieved items. If no items found, succesfull cache result with empty list.
     */
    abstract public function getCollectionItems(string $key, array $permissions, ?string $clientID): CacheResult;
}