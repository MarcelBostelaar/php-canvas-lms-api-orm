<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Core\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Core\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Core\Caching\Utility\CacheRule;
use CanvasApiLibrary\Core\Providers\SectionProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SectionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SectionProviderInterface;

class SectionProviderCached implements SectionProviderInterface{

    use SectionProviderProperties;
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
