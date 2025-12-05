<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheStorage;
use CanvasApiLibrary\Caching\AccessAware\PermissionsHandler;
use CanvasApiLibrary\Core\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderInterface;

class AssignmentProviderCached implements AssignmentProviderInterface{

    use AssignmentProviderProperties;

    public function __construct(
        private readonly AssignmentProviderInterface $wrapped,
        private readonly CacheStorage $cache,
        private readonly int $ttl
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getClientID(): string{
        return $this->wrapped->getClientID();
    }

    public function populateAssignment(\CanvasApiLibrary\Core\Models\Assignment $assignment): \CanvasApiLibrary\Core\Models\Assignment{
        return $this->cache->ensureThenTrySingleValue(
            $assignment->getUniqueId(),
            $this->ttl,
            $assignment->course,
            $this->getClientID(),
            PermissionsHandler::contextFilterCoursebound($assignment->course),
            fn()=> $this->wrapped->populateAssignment($assignment)
        );
    }
}