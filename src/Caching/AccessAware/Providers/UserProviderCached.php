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
    public function getUsersInGroup(GroupStub $group, bool $skipCache = false) : mixed{
        $key = GroupStub::fromStub($group)->getResourceKey();
        $alternativeKey = Group::fromStub($group)->getResourceKey();
        $collectionKey = "getUsersInGroup" . $key;

        //ensure permissions manually.
        if(isset($group->optionalCourseContext)){
            $this->permissionEnsurer->allUsers($group->optionalCourseContext, $this->getClientID(), $skipCache);
        }
        else{
            $this->permissionEnsurer->usersInDomain($group->domain, $this->getClientID(), $skipCache);
        }
    
        $val = $this->unknownPermissionCollectionValue(
            $collectionKey,
            fn() => $this->wrapped->getUsersInGroup($group, $skipCache),
            $skipCache
        );
        
        //Setup permissions union
        $this->cache->setPermissionUnion($key, $alternativeKey);

        //set backpropagate permissions from users to group
        $this->cache->setBackpropagation($collectionKey, $this->permissionHandler::domainCourseUserType(), $alternativeKey);
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
    public function getUsersInSection(SectionStub $section, ?string $enrollmentRoleFilter, bool $skipCache = false) : mixed{
        return $this->userInCourseScopedCollectionValue(
            "getUsersInSection" . SectionStub::fromStub($section)->getResourceKey(),
            fn() => $this->wrapped->getUsersInSection($section, $enrollmentRoleFilter, $skipCache),
            fn(User $x) => [$this->permissionHandler::domainCourseUserPermission($section->course, $x)],
            $skipCache,
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
    public function getUsersInCourse(CourseStub $course, ?string $enrollmentRoleFilter, bool $skipCache = false) : mixed{
        return $this->userInCourseScopedCollectionValue(
            "getUsersInCourse" . CourseStub::fromStub($course)->getResourceKey(),
            fn() => $this->wrapped->getUsersInCourse($course, $enrollmentRoleFilter, $skipCache),
            fn(User $x) => [$this->permissionHandler::domainCourseUserPermission($course, $x)],
            $skipCache,
            $course
        );
    }

    /**
	 * @param Domain $domain
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInDomain(Domain $domain, bool $skipCache = false) : mixed{
        return $this->domainUserScopedCollectionValue(
            "getUsersInDomain" . $domain->getResourceKey(),
            fn() => $this->wrapped->getUsersInDomain($domain, $skipCache),
            $skipCache,
            $domain
        );
    }

    /**
	 * @param UserStub $user
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<User>|UnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function populateUser(UserStub $user, bool $skipCache = false) : mixed{
        if(isset($user->optionalCourseContext)){
            return $this->userCourseAndDomainSingleValue(
                User::fromStub($user)->getResourceKey(),
                fn() => $this->wrapped->populateUser($user, $skipCache),
                $user,
                $user->optionalCourseContext,
                $skipCache
            );
        }
        else{
            return $this->domainUserSingleValue(
                User::fromStub($user)->getResourceKey(),
                fn() => $this->wrapped->populateUser($user, $skipCache),
                $user,
                $skipCache
            );
        }
    }
}
