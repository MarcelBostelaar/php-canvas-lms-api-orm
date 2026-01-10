<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Core\Models\Group;
use CanvasApiLibrary\Core\Models\GroupCategory;
use CanvasApiLibrary\Core\Models\GroupCategoryStub;
use CanvasApiLibrary\Core\Models\GroupStub;
use CanvasApiLibrary\Core\Providers\GroupProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\GroupProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\GroupProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\GroupWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;
use Exception;

/**
 * @implements GroupProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class GroupProviderCached implements GroupProviderInterface{

    use GroupProviderProperties;
    use PermissionEnsurerTrait;
    use GroupWrapperTrait;
    use CacheHelperTrait;
    
    
    public function __construct(
        private readonly GroupProvider $wrapped,
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
     * Ensure that course context is set, if the purpose is to access course-scoped groups, otherwise caching will not work correctly.
     * @param GroupCategoryStub $groupCategory
     * @param bool $skipCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Group[]>|UnauthorizedResult
     */
    public function getAllGroupsInGroupCategory(GroupCategoryStub $groupCategory, bool $skipCache = false, bool $doNotCache = false): mixed{
        $originKey = GroupCategoryStub::fromStub($groupCategory)->getResourceKey();
        $alternativeKey = GroupCategory::fromStub($groupCategory)->getResourceKey();
        $collectionKey = "getAllGroupsInGroupCategory" . $originKey;
        [$skipCache, $doNotCache] = $this->optionalCourseContextPermissionEnsurer(
            $groupCategory,
            $skipCache,
            $doNotCache
        );

        //Groups themselves do not have knowable permissions from just their data.  
        //Their permissions are propagatad back from the users in them.
        $val = $this->unknownPermissionCollectionValue(
            $collectionKey, 
            fn() => $this->wrapped->getAllGroupsInGroupCategory($groupCategory, $skipCache, $doNotCache),
            $skipCache,
            $doNotCache
        );

        if($doNotCache){
            return $val;
        }
        
        //Setup permissions union
        $this->cache->setPermissionUnion($originKey, $alternativeKey);
        //setup backpropagation, 
        // groups and group categories live in both the global (domain) namespace bound to user(s), 
        // and the course specific namespace, bound to user(s). So we configure both.

        // Being allowed to access a group category depends on being allowed to access a group. 
        // Can you access 1 group? You may access the category it belongs to.
        $this->cache->setBackpropagation($collectionKey, $this->permissionHandler::domainUserType(), $originKey);

        return $val;

    }

    /**
     * Ensure that course context is set, if the purpose is to access course-scoped groups, otherwise caching will not work correctly.
     * @param GroupStub $group
     * @param bool $skipCache
     * @return ErrorResult|NotFoundResult|SuccessResult<\CanvasApiLibrary\Core\Models\Group>|UnauthorizedResult
     */
    public function populateGroup(GroupStub $group, bool $skipCache = false, bool $doNotCache = false): mixed{
        [$skipCache, $doNotCache] = $this->optionalCourseContextPermissionEnsurer(
            $group,
            $skipCache,
            $doNotCache
        );

        return $this->unknownPermissionSingleValue(
            Group::fromStub($group)->getResourceKey(),
            fn() => $this->wrapped->populateGroup($group, $skipCache, $doNotCache),
            $skipCache,
            $doNotCache
        );
    }
}
