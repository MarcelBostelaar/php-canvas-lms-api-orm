<?php

namespace CanvasApiLibrary\Caching\Providers;

use CanvasApiLibrary\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Caching\Utility\CacheRule;
use CanvasApiLibrary\Providers\SectionProvider;
use CanvasApiLibrary\Providers\Generated\Traits\SectionProviderProperties;
use CanvasApiLibrary\Providers\Interfaces\SectionProviderInterface;

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

    public function getAllSectionsInCourse(\CanvasApiLibrary\Models\Course $course): array{
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

    public function populateSection(\CanvasApiLibrary\Models\Section $section): \CanvasApiLibrary\Models\Section{
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
