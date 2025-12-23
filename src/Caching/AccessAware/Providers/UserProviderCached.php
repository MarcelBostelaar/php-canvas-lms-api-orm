<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Core\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Core\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Core\Caching\Utility\CacheRule;
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
    public function __construct(
        private readonly UserProvider $wrapped,
        private readonly CacheStorage $cache,
        public readonly int $ttl,
        private readonly PermissionsHandlerInterface $permissionHandler
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getUsersInGroup(\CanvasApiLibrary\Core\Models\Group $group): array{
        $this->doPreCacheCall();
        
        [$cachedItem, $set] = $this->cache->get(
            $this->getUsersInGroupCR,
            $this->wrapped->getClientID(),
            "getUsersInGroup",
            $group);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->getUsersInGroup($group));
    }

    public function getUsersInSection(\CanvasApiLibrary\Core\Models\Section $section, ?string $enrollmentRoleFilter = null): array{
        $this->doPreCacheCall();
        
        [$cachedItem, $set] = $this->cache->get(
            $this->getUsersInSectionCR,
            $this->wrapped->getClientID(),
            "getUsersInSection",
            $section);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->getUsersInSection($section, $enrollmentRoleFilter));
    }

    public function getUsersInCourse(\CanvasApiLibrary\Core\Models\Course $course, ?string $enrollmentRoleFilter = null, bool $skipCache = false): array{
        //DO NOT ENSURE COURSE PERMISSIONS, this method is used to ensure those, otherwise we get infite recursion.
    }

    public function populateUser(\CanvasApiLibrary\Core\Models\User $user): \CanvasApiLibrary\Core\Models\User{
        $this->doPreCacheCall();
        [$cachedItem, $set] = $this->cache->get(
            $this->populateUserCR,
            $this->wrapped->getClientID(),
            "populateUser",
            $user);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->populateUser($user));
    }
}
