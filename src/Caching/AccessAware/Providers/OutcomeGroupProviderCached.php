<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;
use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Outcomegroup;
use CanvasApiLibrary\Core\Models\OutcomegroupStub;
use CanvasApiLibrary\Core\Providers\OutcomegroupProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\OutcomegroupProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\OutcomegroupProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\OutcomegroupWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * @implements OutcomegroupProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class OutcomeGroupProviderCached implements OutcomegroupProviderInterface{

    use OutcomegroupProviderProperties;
    use PermissionEnsurerTrait;
    use OutcomegroupWrapperTrait;
    use CacheHelperTrait;

    
    public function __construct(
        private readonly OutcomegroupProvider $wrapped,
        private readonly CacheProviderInterface $cache,
        public readonly int $ttl,
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
     * Populates an outcome group
     * @param OutcomegroupStub $outcomeGroup
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcomegroup>|UnauthorizedResult
     */
    public function populateOutcomegroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        //use unknown permissions, if fetch fails, cache layer still tries to normally fetch, ensuring all those with permissions get the item
        return $this->unknownPermissionSingleValue(
            Outcomegroup::fromStub($outcomeGroup)->getResourceKey(),
            fn() => $this->wrapped->populateOutcomegroup($outcomeGroup, $skipCache, $doNotCache),
            $skipCache,
            $doNotCache);
    }

    /**
     * Gets all outcome groups in a specified course
     * @param CourseStub $course
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcomegroup[]>|UnauthorizedResult
     */
    public function getOutcomegroupsInCourse(CourseStub $course, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->courseCollectionValueAccessAgnostic( //course collective value
            "getOutcomegroupsInCourse" . $course->getResourceKey(),
            fn() => $this->wrapped->getOutcomegroupsInCourse($course, $skipCache, $doNotCache),
            $skipCache,
            $doNotCache, 
            $course);
    }

    /**
     * Returns all outcomes that are children of the given outcome group.
     * @param OutcomegroupStub $outcomeGroup
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcomegroup[]>|UnauthorizedResult
     */
    public function getSubgroupsOfOutcomegroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        //All subgroups are visible if the group they are in is visible, so we can cache as a access agnostic collection with propagated permissions
        return $this->permissionPropagatedAccessAgnosticCollectionValue(
            "getSubgroupsOfOutcomegroup" . $outcomeGroup->getResourceKey(),
            $outcomeGroup,
            fn() => $this->wrapped->getSubgroupsOfOutcomegroup($outcomeGroup, $skipCache, $doNotCache),
            $skipCache,
            $doNotCache
        );
    }
}
