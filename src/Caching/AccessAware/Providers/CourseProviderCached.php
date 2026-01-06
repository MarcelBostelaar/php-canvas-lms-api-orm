<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Providers\CourseProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\CourseProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\CourseProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\CourseWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * @implements CourseProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class CourseProviderCached implements CourseProviderInterface{

    use CourseProviderProperties;
    use PermissionEnsurerTrait;
    use CourseWrapperTrait;
    use CacheHelperTrait;

    
    public function __construct(
        private readonly CourseProvider $wrapped,
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

    public function getAllCoursesInDomain(Domain $domain, bool $skipCache = false) : SuccessResult|NotFoundResult|ErrorResult|UnauthorizedResult{
        return $this->clientPrivateValue(
            "getAllCoursesInDomain" . $domain->getResourceKey(),
            fn() => $this->wrapped->getAllCoursesInDomain($domain, $skipCache),
            $skipCache
        );
    }
}