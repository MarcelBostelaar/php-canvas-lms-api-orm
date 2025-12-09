<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Core\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Core\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Core\Caching\Utility\CacheRule;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\SubmissionProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SubmissionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SubmissionProviderInterface;

class SubmissionProviderCached implements SubmissionProviderInterface{

    use SubmissionProviderProperties;
    use PrecallTrait;
    public function __construct(
        private readonly SubmissionProvider $wrapped,
        private readonly FullCacheProviderInterface $cache,
        private readonly CacheRule $getSubmissionsInAssignmentCR = new UndefinedCacherule(),
        private readonly CacheRule $populateSubmissionCR = new UndefinedCacherule()
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getSubmissionsInAssignment(\CanvasApiLibrary\Core\Models\Assignment $assignment, ?UserProviderInterface $userProvider = null): array{
        $this->doPreCacheCall();
        
        [$cachedItem, $set] = $this->cache->get(
            $this->getSubmissionsInAssignmentCR,
            $this->wrapped->getClientID(),
            "getSubmissionsInAssignment",
            $assignment);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->getSubmissionsInAssignment($assignment, $userProvider));
    }

    public function populateSubmission(\CanvasApiLibrary\Core\Models\Submission $submission): \CanvasApiLibrary\Core\Models\Submission{
        $this->doPreCacheCall();
        
        [$cachedItem, $set] = $this->cache->get(
            $this->populateSubmissionCR,
            $this->wrapped->getClientID(),
            "populateSubmission",
            $submission);
        if($cachedItem->isCacheHit){
            return $cachedItem->value;
        }
        return $set($this->wrapped->populateSubmission($submission));
    }
}
