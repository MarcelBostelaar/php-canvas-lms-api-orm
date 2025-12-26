<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;
use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\AssignmentStub;
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
    use CacheHelperTrait;

    
    public function __construct(
        private readonly AssignmentProvider $wrapped,
        private readonly CacheProviderInterface $cache,
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

    /**
     * @param AssignmentStub $assignment
     * @param bool $skipCache
     * @return ErrorResult|NotFoundResult|SuccessResult<\CanvasApiLibrary\Core\Models\Assignment>|UnauthorizedResult
     */
    public function populateAssignment(AssignmentStub $assignment, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->courseSingleValue(
            Assignment::fromStub($assignment)->getResourceKey(),
            fn() => $this->wrapped->populateAssignment($assignment, $skipCache),
            $assignment->course,
            $skipCache);
    }
}