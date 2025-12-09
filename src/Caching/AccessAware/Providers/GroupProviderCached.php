<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheStorage;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Core\Providers\GroupProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\GroupProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\GroupProviderInterface;
use Exception;

class GroupProviderCached implements GroupProviderInterface{

    use GroupProviderProperties;
    public function __construct(
        private readonly GroupProvider $wrapped,
        private readonly CacheStorage $cache,
        private readonly int $ttl,
        private readonly PermissionsHandlerInterface $permissionHandler
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getClientID(): string{
        return $this->wrapped->getClientID();
    }

    public function getAllGroupsInGroupCategory(\CanvasApiLibrary\Core\Models\GroupCategory $category): array{
        //call ensure domain perms
        //if item has domain user context, call ensure domain user perms
        //if item has domain course user context, call:
            //ensure domain course perms
            //ensure domain course user perms

        $collectionKey = "getAllGroupsInGroupCategory" . $category->getUniqueId();
        $item = $this->cache->getCollection(
            $collectionKey,
            $this->getClientID()
        );
        if($item->hit){
            return $item->value;
        }
        //No hit
        $actualItems = $this->wrapped->getAllGroupsInGroupCategory($category);

        //Allowed contexts are the all user permissions of DomainUser type in this domain
        $knownFilters = [$this->permissionHandler->contextFilterDomainUser($category->domain)];
        if($category->optionalCourseContext !== null){
            //and user permissions of the DomainCourseUser type in this course (in this domain)
            $knownFilters[] = $this->permissionHandler->contextFilterDomainCourseUser(
                $category->optionalCourseContext
            );
        }

        //Set up collection
        $this->cache->ensureCollection($collectionKey, $this->ttl, ...$knownFilters);
        
        //Set up permission backpropagation from groups.
        //Propagate back all domainuser perms
        $this->cache->setBackpropagation($collectionKey, 
        $this->permissionHandler->domainUserType(),
        $category->getUniqueId());

        //Propagate back all domainCourseUser perms
        $this->cache->setBackpropagation($collectionKey, 
        $this->permissionHandler->domainCourseUserType(),
        $category->getUniqueId());

        //Add individual items to the cache
        foreach($actualItems as $item){
            $this->cache->setCollectionItem(
                $collectionKey, 
                $item->getUniqueId(), 
                $item->withMetaDataStripped(), 
                $this->ttl,
                $this->getClientID());
                //Do not set permissions, as permissions must be backpropagated from the users which come out of the groups.
        }
        //No permissions can be assigned to the client from this endpoint.
        return $actualItems;
    }

    public function populateGroup(\CanvasApiLibrary\Core\Models\Group $group): \CanvasApiLibrary\Core\Models\Group{
        return $this->cache->trySingleValue(
            $group->getUniqueId(),
            $this->ttl,
            $this->getClientID(),
            fn()=> $this->wrapped->populateGroup($group)
        ); //Cannot set up permissions, must be backwards propagated from users in group.
    }
}
