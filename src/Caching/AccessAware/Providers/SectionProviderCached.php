<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Core\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Core\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Core\Caching\Utility\CacheRule;
use CanvasApiLibrary\Core\Providers\SectionProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SectionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SectionProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\SectionWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;


/**
 * @implements SectionProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class SectionProviderCached implements SectionProviderInterface{

    use SectionProviderProperties;
    use PermissionEnsurerTrait;
    use SectionWrapperTrait;
    public function __construct(
        private readonly SectionProvider $wrapped,
        private readonly FullCacheProviderInterface $cache,
        private readonly CacheRule $getAllSectionsInCourseCR = new UndefinedCacherule(),
        private readonly CacheRule $populateSectionCR = new UndefinedCacherule()
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getAllSectionsInCourse(\CanvasApiLibrary\Core\Models\Course $course): array{
        $this->doPreCacheCall();

        [$cachedItem, $set] = $this->cache->get(
            $this->getAllSectionsInCourseCR,
            $this->wrapped->getClientID(),
            "getAllSectionsInCourse",
            $course);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->getAllSectionsInCourse($course));
    }

    public function populateSection(\CanvasApiLibrary\Core\Models\Section $section): \CanvasApiLibrary\Core\Models\Section{
        $this->doPreCacheCall();

        [$cachedItem, $set] = $this->cache->get(
            $this->populateSectionCR,
            $this->wrapped->getClientID(),
            "populateSection",
            $section);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->populateSection($section));
    }
}
