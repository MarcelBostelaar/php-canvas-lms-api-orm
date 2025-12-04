<?php

namespace CanvasApiLibrary\Caching\Providers;

use CanvasApiLibrary\Caching\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Providers\AssignmentProvider;
use CanvasApiLibrary\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Providers\Interfaces\AssignmentProviderInterface;

/**
 * @template METADATA
 */
class AssignmentProviderCached implements AssignmentProviderInterface{

    use AssignmentProviderProperties;

    /**
     * @param CacheProviderInterface<METADATA> $cache
     * @param METADATA $metadataPopulateAssignment
     */
    public function __construct(
        private readonly AssignmentProvider $wrapped,
        private readonly CacheProviderInterface $cache,
        private readonly mixed $metadataPopulateAssignment = null
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function populateAssignment(\CanvasApiLibrary\Models\Assignment $assignment): \CanvasApiLibrary\Models\Assignment{
        $cachedVal = $this->cache->getCached("populateAssignment", $this->metadataPopulateAssignment, $assignment->getMinimumDataRepresentation());
        if($cachedVal->isCacheHit){
            return $cachedVal->value;
        }
        return $this->wrapped->populateAssignment($assignment);
    }
}