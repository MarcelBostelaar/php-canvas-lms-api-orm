<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\Group;
use CanvasApiLibrary\Core\Models\GroupStub;
use CanvasApiLibrary\Core\Models\User;
use CanvasApiLibrary\Core\Models\SectionStub;
use CanvasApiLibrary\Core\Models\UserStub;
use CanvasApiLibrary\Core\Providers\Traits\UserWrapperTrait;
use CanvasApiLibrary\Core\Providers\UserProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\UserProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;


/**
 * @implements UserProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class UserProviderCached implements UserProviderInterface{

    use UserProviderProperties;
    use PermissionEnsurerTrait;
    use UserWrapperTrait;
    use CacheHelperTrait;
    public function __construct(
        private readonly UserProvider $wrapped,
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
	 * @param GroupStub $group
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInGroup(GroupStub $group, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $key = GroupStub::fromStub($group)->getResourceKey();
        $alternativeKey = Group::fromStub($group)->getResourceKey();
        $collectionKey = "getUsersInGroup" . $key;

        [$skipCache, $doNotCache] = $this->optionalCourseContextPermissionEnsurer($group, $skipCache, $doNotCache);
    
        $val = $this->unknownPermissionCollectionValue(
            $collectionKey,
            fn() => $this->wrapped->getUsersInGroup($group, $skipCache, $doNotCache), //TODO fix interfaces
            $skipCache,
            $doNotCache
        );

        if($doNotCache){
            return $val;
        }
        
        //Setup permissions union
        $this->cache->setPermissionUnion($key, $alternativeKey);

        //set backpropagate permissions from users to group
        $this->cache->setBackpropagation($collectionKey, $this->permissionHandler::domainUserType(), $alternativeKey);
        return $val;
    }

    /**
	 * @param SectionStub $section
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInSection(SectionStub $section, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed{
        return $this->userInCourseScopedCollectionValue(
            "getUsersInSection" . SectionStub::fromStub($section)->getResourceKey(),
            fn() => $this->wrapped->getUsersInSection($section, $enrollmentRoleFilter, $skipCache),
            function(User $x) use($section) {
                return [
                    $this->permissionHandler::domainCourseUserPermission($section->course, $x),
                    $this->permissionHandler::domainUserPermission($x)
                ];
            },
            $skipCache,
            $doNotCache,
            $section->course
        );
    }

    /**
	 * @param CourseStub $course
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInCourse(CourseStub $course, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed{
        return $this->userInCourseScopedCollectionValue(
            "getUsersInCourse" . CourseStub::fromStub($course)->getResourceKey(),
            fn() => $this->wrapped->getUsersInCourse($course, $enrollmentRoleFilter, $skipCache),
            function(User $x) use($course) {
                return [
                    $this->permissionHandler::domainCourseUserPermission($course, $x),
                    $this->permissionHandler::domainUserPermission($x)
                ];
            },
            $skipCache,
            $doNotCache,
            $course
        );
    }

    /**
	 * @param UserStub $user
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<User>|UnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function populateUser(UserStub $user, bool $skipCache = false, bool $doNotCache = false) : mixed{
        return $this->userSingleValue(
            User::fromStub($user)->getResourceKey(),
            fn() => $this->wrapped->populateUser($user, $skipCache),
            $user,
            $skipCache,
            $doNotCache
        );
    }
}
