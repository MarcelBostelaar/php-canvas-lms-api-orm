<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Core\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Core\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Core\Caching\Utility\CacheRule;
use CanvasApiLibrary\Core\Providers\GroupProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\GroupProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\GroupProviderInterface;

class GroupProviderCached implements GroupProviderInterface{

    use GroupProviderProperties;
    public function __construct(
        private readonly GroupProvider $wrapped,
        private readonly FullCacheProviderInterface $cache,
        private readonly CacheRule $getAllGroupsInGroupCategoryCR = new UndefinedCacherule(),
        private readonly CacheRule $populateGroupCR = new UndefinedCacherule()
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getAllGroupsInGroupCategory(\CanvasApiLibrary\Core\Models\GroupCategory $category): array{
        [$cachedItem, $set] = $this->cache->get(
            $this->getAllGroupsInGroupCategoryCR,
            $this->wrapped->getClientID(),
            "getAllGroupsInGroupCategory",
            $category);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->getAllGroupsInGroupCategory($category));
    }

    public function populateGroup(\CanvasApiLibrary\Core\Models\Group $group): \CanvasApiLibrary\Core\Models\Group{
        [$cachedItem, $set] = $this->cache->get(
            $this->populateGroupCR,
            $this->wrapped->getClientID(),
            "populateGroup",
            $group);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->populateGroup($group));
    }
}
