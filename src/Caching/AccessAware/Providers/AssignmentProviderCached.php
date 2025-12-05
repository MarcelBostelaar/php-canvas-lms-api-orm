<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Core\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Core\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Core\Caching\Utility\CacheRule;
use CanvasApiLibrary\Core\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderInterface;

class AssignmentProviderCached implements AssignmentProviderInterface{

    use AssignmentProviderProperties;

    public function __construct(
        private readonly AssignmentProviderInterface $wrapped,
        private readonly FullCacheProviderInterface $cache,
        private readonly CacheRule $populateAssignmentCR = new UndefinedCacherule()
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getClientID(): string{
        return $this->wrapped->getClientID();
    }

    public function populateAssignment(\CanvasApiLibrary\Core\Models\Assignment $assignment): \CanvasApiLibrary\Core\Models\Assignment{
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