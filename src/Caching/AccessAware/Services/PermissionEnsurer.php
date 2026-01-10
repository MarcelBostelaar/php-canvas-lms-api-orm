<?php

namespace CanvasApiLibrary\Caching\AccessAware\Services;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\CourseProviderCached;
use CanvasApiLibrary\Caching\AccessAware\Providers\UserProviderCached;
use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\UserStub;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use Closure;

/**
 * Gets permissions
 */
class PermissionEnsurer{
    /**
     * Summary of __construct
     * @param CourseProviderCached $courseProvider
     * @param UserProviderCached $userProvider
     * @param CacheProviderInterface $cache
     * @param Closure(NotFoundResult) $handleNotFound
     * @param Closure(ErrorResult) $handleOtherError
     */
    public function __construct(
        private readonly CourseProviderCached $courseProvider,
        private readonly UserProviderCached $userProvider,
        private readonly CacheProviderInterface $cache,
        private readonly PermissionsHandlerInterface $permissionHandler,
        private readonly Closure $handleNotFound,
        private readonly Closure $handleOtherError) {
    }

    public function domain(Domain $domain, string $clientID, bool $refresh): bool{
        $key = "DomainPermissions" . $clientID;
        if($this->cache->getPrivate($key, $clientID)->hit && !$refresh){
            return true;
        }
        $result = $this->courseProvider->getAllCoursesInDomain($domain, true);
        $allowed = false;
        if($result instanceof SuccessResult){
            $allowed = true;
        }
        if($result instanceof UnauthorizedResult){
            $allowed = false;
        }
        if($result instanceof NotFoundResult){
            return ($this->handleNotFound)($result);
        }
        if($result instanceof ErrorResult){
            return ($this->handleOtherError)($result);
        }
        $this->cache->setPrivate($key, $allowed, $this->courseProvider->ttl, $clientID);
        return $allowed;
    }

    public function usersInCourse(CourseStub $course, string $clientID, bool $refresh): bool{
        $key = "CourseUserPermissions" . $course->getResourceKey() . $clientID;
        if($this->cache->getPrivate($key, $clientID)->hit && !$refresh){
            return true;
        }
        if(!$this->domain($course->domain, $clientID, $refresh)){
            return false;
        }
        $result = $this->userProvider->getUsersInCourse($course, null, true);
        $allowed = false;
        if($result instanceof SuccessResult){
            $allowed = true;
        }
        if($result instanceof UnauthorizedResult){
            $allowed = false;
        }
        if($result instanceof NotFoundResult){
            //assume information masking, not allowed to know course exists
            $allowed = false;
        }
        if($result instanceof ErrorResult){
            return ($this->handleOtherError)($result);
        }
        $this->cache->setPrivate($key, $allowed, $this->userProvider->ttl, $clientID);
        return $allowed;
    }
}