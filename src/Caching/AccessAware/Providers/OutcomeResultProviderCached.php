<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;
use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\OutcomeResult;
use CanvasApiLibrary\Core\Models\OutcomeStub;
use CanvasApiLibrary\Core\Models\UserStub;
use CanvasApiLibrary\Core\Providers\OutcomeResultProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\OutcomeResultProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\OutcomeResultProviderInterface;
use CanvasApiLibrary\Core\Providers\OutcomeResultProviderManualTrait;
use CanvasApiLibrary\Core\Providers\Traits\OutcomeResultWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * Does not support individual outcome population, as that is incredibly inefficient and has little use case.
 * @implements OutcomeResultProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class OutcomeResultProviderCached implements OutcomeResultProviderInterface{

    use OutcomeResultProviderProperties;
    use OutcomeResultProviderManualTrait;
    use PermissionEnsurerTrait;
    use OutcomeResultWrapperTrait;
    use CacheHelperTrait;

    
    public function __construct(
        private readonly OutcomeResultProvider $wrapped,
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
     * Gets the total outcomes in a course. Can be filtered to specific users.
     * @param CourseStub $course
     * @param UserStub[] $users
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<OutcomeResult[]>|UnauthorizedResult
     */
    public function getOutcomeResultsInCourse(CourseStub $course, array $users = [], bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        //Current solution is not extremely optimal, as we cache each specific configuration of user filters as if it is their own method
        //But at the time of coding this a more complex solution has not been written that would take into account the various shortcuts that could be taken
        //With this solution, validity is ensured, at the cost of not caching filter subsets for clients with valid permissions for both sets/the subset
        
        //manually ensure all permissions for users in this course are on the client
        $this->permissionEnsurer->usersInCourse($course, $this->getClientID(), false);
        return $this->userScopedCollectionValue(
            "getOutcomeResultsInCourse" . 
            $course->getResourceKey() . 
            "filtered" . 
            implode(",", 
                array_map(
                    fn($x)=>$x->getResourceKey(),
                    $users
                )
            ),
            fn() => $this->wrapped->getOutcomeResultsInCourse($course, $users, $skipCache, $doNotCache),
            fn(OutcomeResult $x) => [$this->permissionHandler::domainUserPermission($x->user)],
            $course->domain,
            $skipCache,
            $doNotCache
        );
    }
}
