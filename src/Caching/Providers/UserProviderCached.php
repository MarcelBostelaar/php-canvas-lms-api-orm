<?php

namespace CanvasApiLibrary\Caching\Providers;

use CanvasApiLibrary\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Caching\Utility\CacheRule;
use CanvasApiLibrary\Providers\UserProvider;
use CanvasApiLibrary\Providers\Generated\Traits\UserProviderProperties;
use CanvasApiLibrary\Providers\Interfaces\UserProviderInterface;

class UserProviderCached implements UserProviderInterface{

    use UserProviderProperties;
    public function __construct(
        private readonly UserProvider $wrapped,
        private readonly FullCacheProviderInterface $cache,
        private readonly CacheRule $getUsersInGroupCR = new UndefinedCacherule(),
        private readonly CacheRule $getUsersInSectionCR = new UndefinedCacherule(),
        private readonly CacheRule $populateUserCR = new UndefinedCacherule()
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function asAdmin(): UserProviderCached{
        $cloned = clone $this;
        $cloned->wrapped = $this->wrapped->asAdmin();
        return $cloned;
    }

    public function withinCourse(\CanvasApiLibrary\Models\Course $course): UserProviderCached{
        $cloned = clone $this;
        $cloned->wrapped = $this->wrapped->withinCourse($course);
        return $cloned;
    }

    public function getUsersInGroup(\CanvasApiLibrary\Models\Group $group): array{
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

    public function getUsersInSection(\CanvasApiLibrary\Models\Section $section, ?string $enrollmentRoleFilter = null): array{
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

    public function populateUser(\CanvasApiLibrary\Models\User $user): \CanvasApiLibrary\Models\User{
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
