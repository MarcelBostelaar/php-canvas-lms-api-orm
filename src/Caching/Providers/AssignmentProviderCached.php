<?php

namespace CanvasApiLibrary\Caching\Providers;

use CanvasApiLibrary\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Caching\Utility\CacheRule;
use CanvasApiLibrary\Providers\AssignmentProvider;
use CanvasApiLibrary\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Providers\Interfaces\AssignmentProviderInterface;

class AssignmentProviderCached implements AssignmentProviderInterface{

    use AssignmentProviderProperties;

    public function __construct(
        private readonly AssignmentProvider $wrapped,
        private readonly FullCacheProviderInterface $cache,
        private readonly CacheRule $populateAssignmentCR = new UndefinedCacherule()
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function populateAssignment(\CanvasApiLibrary\Models\Assignment $assignment): \CanvasApiLibrary\Models\Assignment{
        [$cachedItem, $set] = $this->cache->get(
            $this->populateAssignmentCR,
            $this->wrapped->getClientID(),
            "populateAssignment",
            $assignment);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->populateAssignment($assignment));
    }
}