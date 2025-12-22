<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheStorage;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Core\Providers\AssignmentProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\AssignmentWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * @implements AssignmentProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class AssignmentProviderCached implements AssignmentProviderInterface{

    use AssignmentProviderProperties;
    use PermissionEnsurerTrait;
    use AssignmentWrapperTrait;

    
    public function __construct(
        private readonly AssignmentProvider $wrapped,
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

    public function populateAssignment(\CanvasApiLibrary\Core\Models\Assignment $assignment): \CanvasApiLibrary\Core\Models\Assignment{
        $this->permissionEnsurer?->all();
        return $this->cache->ensureThenTrySingleValue(
            $assignment->getUniqueId(),
            $this->ttl,
            $assignment->course,
            $this->getClientID(),
            $this->permissionHandler::contextFilterDomainCourse($assignment->course),
            fn()=> $this->wrapped->populateAssignment($assignment)
        );
    }
}