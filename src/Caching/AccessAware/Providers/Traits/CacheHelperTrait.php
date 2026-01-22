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
     * Value saved per client. Can be both single model or array of models (collection). Dominance matching not applied because this is per-client.
     * @param string $key
     * @param Closure $valueFactory
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return SuccessResult<ModelInterface>|ErrorResult|UnauthorizedResult|NotFoundResult
     */
    private function clientPrivateValue(string $key, Closure $valueFactory, bool $skipCache, bool $doNotCache){
        $modifiedKey = $this->getClientID() . "_" . $key;
        if(!$skipCache){
            $cached = $this->cache->get($this->getClientID(), $modifiedKey);
            if($cached->hit){
                return new SuccessResult($cached->value);
            }
        }
        $value = $valueFactory();
        $clientPermission = $this->permissionHandler->clientPermission($this->getClientID());
        if($value instanceof SuccessResult && !$doNotCache){
            if(is_array($value->value )){
                $cleaned = array_map(fn($x) => $x->withMetaDataStripped(), $value->value);
            }
            else{
                $cleaned = $value->value->withMetaDataStripped();
            }
            $this->cache->set($modifiedKey, $cleaned, $this->ttl, $this->getClientID(), $clientPermission);
        }
        return $value;
    }
    
    /**
     * Attempts to retrieve item from cache, if not cached, retrieves via factory, and if succesfull, caches result.
     * @param string $key Key to identify this object.
     * @param Closure(): SuccessResult<ModelInterface>|ErrorResult|UnauthorizedResult|NotFoundResult $valueFactory
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @param bool $doNotCache Whether or not to cache the result after retrieval.
     * @param mixed $permissionRequired The permission object for this item.
     * @return SuccessResult<ModelInterface>|ErrorResult|UnauthorizedResult|NotFoundResult
     */
    private function givenPermissionsSingleValue(string $key, Closure $valueFactory, bool $skipCache, bool $doNotCache, mixed $permissionRequired): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
        if(!$skipCache){
            $cached = $this->cache->get($this->getClientID(), $key);
            if($cached->hit){
                return new SuccessResult($cached->value);
            }
        }
        $value = $valueFactory();
        $permissionsRequired = $permissionRequired === null ? [] : [$permissionRequired];
        if($value instanceof SuccessResult && !$doNotCache){
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
     * @param bool $doNotCache Whether or not to cache the result after retrieval.
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult
     */
    private function userInCourseSingleValue(string $key, Closure $valueFactory, UserStub $user, CourseStub $course, bool $skipCache, bool $doNotCache): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
        $this->permissionEnsurer->usersInCourse($course, $this->getClientID(), $skipCache);
        $requiredPermission = $this->permissionHandler::domainCourseUserPermission($course, $user);
        return $this->givenPermissionsSingleValue($key, $valueFactory, $skipCache, $doNotCache, $requiredPermission);
    }
    /**
     * Attempts to retrieve item from cache. If not cached, retrieves via factory, and if succesfull, caches result permission restricted to the given course.
     * @param string $key $Key by which to identify this item.
     * @param Closure(): ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult $valueFactory
     * @param CourseStub $course The course to use to add the permission
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @param bool $doNotCache Whether or not to cache the result after retrieval.
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult
     */
    private function courseSingleValue(string $key, Closure $valueFactory, CourseStub $course, bool $skipCache, bool $doNotCache): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
        $this->permissionEnsurer->usersInCourse($course, $this->getClientID(), $skipCache);
        $requiredPermission = $this->permissionHandler::domainCoursePermission($course);
        return $this->givenPermissionsSingleValue($key, $valueFactory, $skipCache, $doNotCache, $requiredPermission);
    }

    private function userSingleValue(string $key, Closure $valueFactory, UserStub $user, bool $skipCache, bool $doNotCache): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
        //No need to ensure permissions for single user fetch.
        //If client does not have permission to see user, cache will try to fetch and fail.
        //If client has permission to see user, cache will fetch it correctly.
        //If client has not yet set up permissions for this user, the successful fetch will set it up.
        $requiredPermission = $this->permissionHandler::domainUserPermission($user);
        return $this->givenPermissionsSingleValue($key, $valueFactory, $skipCache, $doNotCache, $requiredPermission);
    }

    /**
     * Try single value. Does not do any permission ensuring, do this before calling manually.
     * @param string $key
     * @param Closure $valueFactory
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface>|UnauthorizedResult
     */
    private function unknownPermissionSingleValue(string $key, Closure $valueFactory, bool $skipCache, bool $doNotCache): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
        return $this->givenPermissionsSingleValue($key, $valueFactory, $skipCache, $doNotCache, null);
    }

    /**
     * Attempts to retrieve a collection from cache based on known permissions of the current client. 
     * If not cached, retrieves via factory, and if succesfull, caches result permission restricted to the given context.
     * @param string $key They key by which to identify this collection.
     * @param Closure(): SuccessResult<ModelInterface[]>|ErrorResult|UnauthorizedResult|NotFoundResult $valueFactory 
     * @param Closure(ModelInterface): mixed $childPermissionFactory Factory that takes a child result (a model) and returns the relevant permission for it.
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @param bool $doNotCache Whether or not to cache the result after retrieval.
     * @param mixed $permissionContextFilter A context filter that scopes the child permissions that will be valid for this permission testing of the collection.
     *      Must correspond to the permissions generated by the child permission factory.
     * @return SuccessResult<ModelInterface[]>|ErrorResult|UnauthorizedResult|NotFoundResult
     */
    private function _internalTryCollectionValue(
        string $key, 
        Closure $valueFactory, 
        Closure $childPermissionFactory, 
        bool $skipCache, 
        bool $doNotCache,
        mixed $permissionContextFilter): SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult
    {
        if(!$skipCache){
            $cached = $this->cache->getCollection($this->getClientID(), $key);
            if($cached->hit){
                return new SuccessResult($cached->value);
            }
        }
        $value = $valueFactory();
        if(!$value instanceof SuccessResult || $doNotCache){
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
            $permissionContextFilter);
        return $value;
    }

    /**
     * Attempts to retrieve a collection from cache based on known permissions of the current client. 
     * If not cached, retrieves via factory, and if succesfull, caches result permission restricted to this course, and the users it belongs to.
     * @param string $key They key by which to identify this collection.
     * @param Closure(): SuccessResult<ModelInterface[]>|ErrorResult|UnauthorizedResult|NotFoundResult $valueFactory 
     * @param Closure(ModelInterface): mixed[] $childPermissionFactory Factory that takes a child result (a model) and returns the relevant permission for it.
     * @param bool $skipCache Whether or not to skip the cache and refresh the resource. If true, will always retrieve fresh value and update the cache.
     * @param bool $doNotCache Whether or not to cache the result after retrieval.
     * @param CourseStub $course The course for which this collection is permission scoped.
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface[]>|UnauthorizedResult
     */
    private function userInCourseScopedCollectionValue(
        string $key, 
        Closure $valueFactory, 
        Closure $childPermissionFactory, 
        bool $skipCache,
        bool $doNotCache,
        CourseStub $course) : SuccessResult|ErrorResult|UnauthorizedResult|NotFoundResult{
            $this->permissionEnsurer->usersInCourse($course, $this->getClientID(), $skipCache);
            $permissionFilter = $this->permissionHandler::contextFilterDomainCourseAnyUser($course);
            return $this->_internalTryCollectionValue($key, $valueFactory, $childPermissionFactory, $skipCache, $doNotCache, $permissionFilter);
    }

    /**
     * Attempts to retrieve a collection from cache based on known permissions of the current client. 
     * If not cached, retrieves via factory, and if succesfull, caches result permission restricted
     * to the given course.
     * Unlike other collection methods, this assumes that any user in the course will always see the same collection,
     * thus the resulting list is the same for any user that can access the course.
     * @param string $key
     * @param Closure $valueFactory
     * @param bool $skipCache
     * @param CourseStub $course
     */
    private function courseCollectionValueAccessAgnostic(
        string $key, 
        Closure $valueFactory,
        bool $skipCache,
        bool $doNotCache,
        CourseStub $course
    ){
        $this->permissionEnsurer->domain($course->domain, $this->getClientID(), false);
        if(!$skipCache){
            $cached = $this->cache->get($this->getClientID(), $key);
            if($cached->hit){
                return new SuccessResult($cached->value);
            }
        }
        $value = $valueFactory();
        $permissionsRequired = $this->permissionHandler::domainCoursePermission($course);
        if($value instanceof SuccessResult && !$doNotCache){
            $cleaned = array_map(fn($x) => $x->withMetaDataStripped(), $value->value);
            $this->cache->set($key, $cleaned, $this->ttl, $this->getClientID(), $permissionsRequired);
        }
        return $value;
    }

    /**
     * Attempts to retrieve a collection value that is access agnostic and has no specific context, but is permission secured.
     * Meaning, the result of this call is the same for all users, if they have any of the permissions, but a permissions is needed.
     * Copies all known permissions from the origin item to the child items.
     * Caches individual child items on their resource key.
     * @param string $key
     * @param ModelInterface $originItem
     * @param Closure $valueFactory
     * @param bool $skipCache
     * @param bool $doNotCache
     */
    private function permissionPropagatedAccessAgnosticCollectionValue(
        string $key,
        ModelInterface $originItem,
        Closure $valueFactory,
        bool $skipCache,
        bool $doNotCache
    ){
        if($skipCache){
            return $valueFactory();
        }
        $parentPermissions = $this->cache->getPermissions($originItem->getResourceKey());
        $collection = $this->givenPermissionsSingleValue( 
            $key,
            $valueFactory,
            $skipCache,
            $doNotCache,
            $parentPermissions
        );
        //save individual outcomes as well
        if($collection instanceof SuccessResult && !$doNotCache){
            foreach($collection as $item){
                $this->cache->set(
                    $item->getResourceKey(),
                    $item,
                    $this->ttl,
                    $this->getClientID(),
                    $parentPermissions
                );
            }
        }
        return $collection;
    }

    /**
     * Try a user scoped collection value. Does not do permission ensuring, do manually before calling.
     * @param string $key
     * @param Closure $valueFactory 
     * @param Closure(ModelInterface) : mixed[] $userPermissionFactory Takes the child item and produces a corresponding domain-user permission
     * @param Domain $domain
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface[]>|UnauthorizedResult
     */
    private function userScopedCollectionValue(
        string $key, 
        Closure $valueFactory,
        Closure $userPermissionFactory,
        Domain $domain,
        bool $skipCache,
        bool $doNotCache
    ){
        return $this->_internalTryCollectionValue(
            $key,
            $valueFactory,
            $userPermissionFactory,
            $skipCache,
            $doNotCache,
            $this->permissionHandler::contextFilterDomainAnyUser($domain)
        );
    }

    /**
     * Try collection value. Does not do any permission ensuring, do this before calling manually.
     * @param string $key
     * @param Closure $valueFactory
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<ModelInterface[]>|UnauthorizedResult
     */
    private function unknownPermissionCollectionValue(
        string $key, 
        Closure $valueFactory,
        bool $skipCache,
        bool $doNotCache
    ){
        return $this->_internalTryCollectionValue(
            $key,
            $valueFactory,
            fn($x) => null,
            $skipCache,
            $doNotCache,
            null
        );
    }

    /**
     * Summary of optionalCourseContextPermissionEnsurer
     * @param mixed $model
     * @param bool $skipCache
     * @param bool $oldDoNotCache
     * @return array{0: bool, 1: bool, 2: bool} //[newSkipCache, newSkipCache]
     */
    private function optionalCourseContextPermissionEnsurer(mixed $model, bool $skipCache, bool $oldDoNotCache): array{
        if($model->optionalCourseContext !== null){
            $this->permissionEnsurer->usersInCourse($model->optionalCourseContext, $this->getClientID(), $skipCache);
            return [$skipCache, $oldDoNotCache];
        }
        else{
            //cannot guarantee permissions, skip cache, additionally, do not cache result.
            return [true, true];
        }
    }

}