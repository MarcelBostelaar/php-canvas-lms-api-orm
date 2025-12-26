<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers\Traits;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Services\PermissionEnsurer;
use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\UserStub;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use CanvasApiLibrary\Core\Models\Utility\ModelInterface;
use Closure;

/**
 * @property CacheProviderInterface $cache 
 * @property PermissionsHandlerInterface $permissionHandler
 * @property PermissionEnsurer $permissionEnsurer
 * @property integer $ttl 
 */
trait CacheHelperTrait{
    abstract public function getClientID(): string;
    
    /**
     * Attempts to retrieve item from cache, if not cached, retrieves via factory, and if succesfull, caches result.
     * @param string $key Key to identify this object.
     * @param Closure(): SuccessResult<ModelInterface>|ErrorResult|UnauthorizedResult|NotFoundResult $valueFactory
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @param mixed[] $permissionsRequired The permission object for this item.
     * @return SuccessResult<ModelInterface>|ErrorResult|UnauthorizedResult|NotFoundResult
     */
    private function _internalTrySingleValue(string $key, Closure $valueFactory, bool $skipCache, mixed ...$permissionsRequired): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
        if(!$skipCache){
            $cached = $this->cache->get($this->getClientID(), $key);
            if($cached->hit){
                return new SuccessResult($cached->value);
            }
        }
        $value = $valueFactory();
        if($value instanceof SuccessResult){
            $this->cache->set($key, $value->value->withMetaDataStripped(), $this->ttl, $this->getClientID(), ...$permissionsRequired);
        }
        return $value;
    }

    /**
     * Attempts to retrieve item from cache. If not cached, retrieves via factory, and if succesfull, caches result permission restricted to the given user in a given course.
     * @param string $key $Key by which to identify this item.
     * @param Closure(): ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult $valueFactory
     * @param UserStub $user The user to use to add the permission
     * @param CourseStub $course The course to use to add the permission
     * @param bool $skipCache  Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult
     */
    private function userInCourseSingleValue(string $key, Closure $valueFactory, UserStub $user, CourseStub $course, bool $skipCache): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
        $this->permissionEnsurer->usersInCourse($course, $this->getClientID(), $skipCache);
        $requiredPermission = $this->permissionHandler::domainCourseUserPermission($course, $user);
        return $this->_internalTrySingleValue($key, $valueFactory, $skipCache, $requiredPermission);
    }
    /**
     * Attempts to retrieve item from cache. If not cached, retrieves via factory, and if succesfull, caches result permission restricted to the given course.
     * @param string $key $Key by which to identify this item.
     * @param Closure(): ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult $valueFactory
     * @param CourseStub $course The course to use to add the permission
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult
     */
    private function courseSingleValue(string $key, Closure $valueFactory, CourseStub $course, bool $skipCache): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
        $this->permissionEnsurer->usersInCourse($course, $this->getClientID(), $skipCache);
        $requiredPermission = $this->permissionHandler::domainCoursePermission($course);
        return $this->_internalTrySingleValue($key, $valueFactory, $skipCache, $requiredPermission);
    }

    /**
     * Try single value. Does not do any permission ensuring, do this before calling manually.
     * @param string $key
     * @param Closure $valueFactory
     * @param bool $skipCache
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult
     */
    private function unknownPermissionSingleValue(string $key, Closure $valueFactory, bool $skipCache){
        return $this->_internalTrySingleValue($key, $valueFactory, $skipCache);
    }

    /**
     * Attempts to retrieve a collection from cache based on known permissions of the current client. 
     * If not cached, retrieves via factory, and if succesfull, caches result permission restricted to the given context.
     * @param string $key They key by which to identify this collection.
     * @param Closure(): SuccessResult<ModelInterface[]>|ErrorResult|UnauthorizedResult|NotFoundResult $valueFactory 
     * @param Closure(ModelInterface): mixed[] $childPermissionFactory Factory that takes a child result (a model) and returns the relevant permission for it.
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @param mixed[] $permissionContextFilter A context filter that scopes the child permissions that will be valid for this permission testing of the collection.
     *      Must correspond to the permissions generated by the child permission factory.
     * @return SuccessResult<ModelInterface[]>|ErrorResult|UnauthorizedResult|NotFoundResult
     */
    private function _internalTryCollectionValue(
        string $key, 
        Closure $valueFactory, 
        Closure $childPermissionFactory, 
        bool $skipCache, 
        mixed ...$permissionContextFilter): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult
    {
        if(!$skipCache){
            $cached = $this->cache->getCollection($this->getClientID(), $key);
            if($cached->hit){
                return new SuccessResult($cached->value);
            }
        }
        $value = $valueFactory();
        if(!$value instanceof SuccessResult){
            return $value;
        }
        $result = $value->value;

        //save child model results individually first
        $childKeys = [];
        foreach($result as $child){
            $childKey = $child->getResourceKey();
            $childKeys[] = $childKey;
            $childPermissions = $childPermissionFactory($child);
            $this->cache->set(
                $childKey, 
                $child->withMetaDataStripped(), 
                $this->ttl, 
                $this->getClientID(), 
                ...$childPermissions);
        }
        //set collection.
        $this->cache->setCollection(
            $this->getClientID(), 
            $key,
            $childKeys, 
            $this->ttl, 
            ...$permissionContextFilter);
        return $value;
    }

    /**
     * Attempts to retrieve a collection from cache based on known permissions of the current client. 
     * If not cached, retrieves via factory, and if succesfull, caches result permission restricted to this course, and the users it belongs to.
     * @param string $key They key by which to identify this collection.
     * @param Closure(): SuccessResult<ModelInterface[]>|ErrorResult|UnauthorizedResult|NotFoundResult $valueFactory 
     * @param Closure(ModelInterface): mixed[] $childPermissionFactory Factory that takes a child result (a model) and returns the relevant permission for it.
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @param CourseStub $course The course for which this collection is permission scoped.
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface[]>|UnauthorizedResult
     */
    private function userInCourseScopedCollectionValue(
        string $key, 
        Closure $valueFactory, 
        Closure $childPermissionFactory, 
        bool $skipCache,
        CourseStub $course) : SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
            $this->permissionEnsurer->usersInCourse($course, $this->getClientID(), $skipCache);
            $permissionFilter = [$this->permissionHandler::contextFilterDomainCourseUser($course)];
            return $this->_internalTryCollectionValue($key, $valueFactory, $childPermissionFactory, $skipCache, $permissionFilter);
    }

    /**
     * Attempts to retrieve a collection from cache based on known permissions of the current client. 
     * If not cached, retrieves via factory, and if succesfull, caches result permission restricted to this course.
     * @param string $key They key by which to identify this collection.
     * @param Closure(): SuccessResult<ModelInterface[]>|ErrorResult|UnauthorizedResult|NotFoundResult $valueFactory
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @param CourseStub $course The course for which this collection is permission scoped.
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface[]>|UnauthorizedResult
     */
    private function courseScopedCollectionValue(
        string $key, 
        Closure $valueFactory,
        bool $skipCache,
        CourseStub $course): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
            $this->permissionEnsurer->usersInCourse($course, $this->getClientID(), $skipCache);
            $permissionFilter = $this->permissionHandler::contextFilterDomainCourse($course);

            //Permission for all child items are also course scoped, so we can just give all of them the same domainCourse permission.
            $permissionFactory = fn($x) => [$this->permissionHandler::domainCoursePermission($course)];

            return $this->_internalTryCollectionValue($key, $valueFactory, $permissionFactory, $skipCache, $permissionFilter);
    }

    private function domainScopedCollectionValue(
        string $key, 
        Closure $valueFactory,
        bool $skipCache,
        Domain $domain): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
            $this->permissionEnsurer->domain($domain, $this->getClientID(), $skipCache);
            $permissionFilter = $this->permissionHandler::contextFilterDomain($domain);

            //Permission for all child items are also domain scoped, so we can just give all of them the same domain permission.
            $permissionFactory = fn($x) => [$this->permissionHandler::domainPermission($domain)];
            
            return $this->_internalTryCollectionValue($key, $valueFactory, $permissionFactory, $skipCache, $permissionFilter);
        }

    private function domainUserScopedCollectionValue(
        string $key, 
        Closure $valueFactory,
        bool $skipCache,
        Domain $domain
    ){
        $this->permissionEnsurer->usersInDomain($domain, $this->getClientID(), $skipCache);
        $permissionFilter = $this->permissionHandler::contextFilterDomainUser($domain);

        //Permission for all child items are also domain scoped, so we can just give all of them the same domain permission.
        $permissionFactory = fn($x) => [$this->permissionHandler::domainUserPermission($x)];
        
        return $this->_internalTryCollectionValue($key, $valueFactory, $permissionFactory, $skipCache, $permissionFilter);
    }

    private function userCourseAndGlobalScopedCollectionValue(
        string $key, 
        Closure $valueFactory,
        bool $skipCache,
        CourseStub $course){
            $this->permissionEnsurer->allUsers($course, $this->getClientID(), $skipCache);
            $permissionFilters = [
                $this->permissionHandler::contextFilterDomainUser($course->domain),
                $this->permissionHandler::contextFilterDomainCourseUser($course)
            ];

            //Permission for all child items are also domain scoped, so we can just give all of them the same domain permission.
            $permissionFactory = fn(UserStub $x) => [
                $this->permissionHandler::domainUserPermission($x),
                $this->permissionHandler::domainCourseUserPermission($course, $x)
            ];
            
            return $this->_internalTryCollectionValue($key, $valueFactory, $permissionFactory, $skipCache, ...$permissionFilters);
        }

    /**
     * Try collection value. Does not do any permission ensuring, do this before calling manually.
     * @param string $key
     * @param Closure $valueFactory
     * @param bool $skipCache
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface[]>|UnauthorizedResult
     */
    private function unknownPermissionCollectionValue(
        string $key, 
        Closure $valueFactory,
        bool $skipCache
    ){
        return $this->_internalTryCollectionValue(
            $key,
            $valueFactory,
            fn($x) => [],
            $skipCache
        );
    }
}