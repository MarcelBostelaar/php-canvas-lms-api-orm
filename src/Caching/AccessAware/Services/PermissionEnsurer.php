<?php

namespace CanvasApiLibrary\Caching\AccessAware\Services;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Providers\Interfaces\CourseProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;

/**
 * Gets permissions
 */
class PermissionEnsurer{
    public function __construct(
        private readonly CourseProviderInterface $courseProvider,
        private readonly UserProviderInterface $userProvider,
        private readonly CacheProviderInterface $cache) {
    }

    public function domain(Domain $domain, string $clientID): bool{
        if($this->cache->getUnprotected("DomainPermissions" . $clientID)){
            return true;
        }
        $this->courseProvider->getAllCoursesInDomain($domain);
        //get self info of user
    }

    public function course(Course $course, string $clientID): bool{
        if(!$this->domain($course->domain, $clientID)){
            return false;
        }
        //get basic info of course
    }

    public function usersInCourse(Course $course, string $clientID): bool{
        if(!$this->course($course, $clientID)){
            return false;
        }
        //get all users in course
    }

    public function usersInDomain(Domain $domain, string $clientID): bool{
        if(!$this->domain($domain, $clientID)){
            return false;
        }
        //get all globally accesible users
    }

    public function allUsers(Course $course, string $clientID){
        $canAccessInCourse = $this->usersInCourse($course, $clientID);
        $this->usersInDomain($course->domain, $clientID);
        return $canAccessInCourse;
    }
}