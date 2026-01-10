<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\SectionStub;
use CanvasApiLibrary\Core\Providers\SectionProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SectionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SectionProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\SectionWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;


/**
 * @implements SectionProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class SectionProviderCached implements SectionProviderInterface{

    use SectionProviderProperties;
    use PermissionEnsurerTrait;
    use SectionWrapperTrait;
    use CacheHelperTrait;

    public function __construct(
        private readonly SectionProvider $wrapped,
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
     * @param CourseStub $course
     * @param bool $skipCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Section[]>|UnauthorizedResult
     */
    public function getAllSectionsInCourse(CourseStub $course, bool $skipCache = false, bool $doNotCache = false): mixed{
        //Assume any user in course can see all sections, thus the resulting list is the same for any user in the course
        return $this->courseCollectionValueAccessAgnostic(
            "getAllSectionsInCourse" . CourseStub::fromStub($course)->getResourceKey(),
            fn() => $this->wrapped->getAllSectionsInCourse($course, $skipCache, $doNotCache),
            $skipCache,
            $doNotCache,
            $course
        );
    }

    /**
     * @param SectionStub $section
     * @param bool $skipCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Section>|UnauthorizedResult
     */
    public function populateSection(SectionStub $section, bool $skipCache = false, bool $doNotCache = false): mixed{
        return $this->courseSingleValue(
            Section::fromStub($section)->getResourceKey(),
            fn() => $this->wrapped->populateSection($section, $skipCache, $doNotCache),
            $section->course,
            $skipCache,
            $doNotCache
        );
    }
}
