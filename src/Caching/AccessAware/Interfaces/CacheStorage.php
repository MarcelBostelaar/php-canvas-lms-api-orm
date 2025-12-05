<?php

namespace CanvasApiLibrary\Caching\AccessAware\Interfaces;

use CanvasApiLibrary\Caching\AccessAware\CacheResult;
use CanvasApiLibrary\Caching\AccessAware\PermissionsHandler;

class CacheStorage implements CacheStorageInterface{

    public function __construct(private readonly CacheProvider $cache) {
    }

    public function hasPermissionsInContext(string $clientID, string $examplePermission): bool{
        return $this->cache->hasPermissionsInContext($clientID, $examplePermission);
    }

    public function setSingleItem(string $key, mixed $value, int $ttl, 
    string $permissionRequired, string $clientID){
        $this->cache->set($key, $value, $ttl, $permissionRequired);
        $this->cache->addPermission($clientID, $permissionRequired, $ttl);
    }

    public function setCollectionItem(string $collectionKey, string $itemKey, mixed $value, int $ttl, 
    string $itemPermissionRequired, string $clientID){
        $this->cache->set($itemKey, $value, $ttl, $itemPermissionRequired);
        $this->cache->addPermission($clientID, $itemPermissionRequired, $ttl);
        $this->cache->ensureCollection($collectionKey, $itemPermissionRequired, $ttl);
        $this->cache->addToCollection($collectionKey, $itemKey, $ttl);
    }
    
    public function setCollection(string $collectionKey, array $itemKeys, int $ttl, 
    string $clientID){
        $this->cache->ensureCollection($collectionKey, null, $ttl);
        $this->cache->setCollectionSet($clientID, $collectionKey, $itemKeys, $ttl);
    }
    
    public function getSingleItem(string $key, string $clientID): CacheResult{
        return $this->cache->get($clientID, $key);
    }
    
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
        return new CacheResult(null, false, true, false);
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