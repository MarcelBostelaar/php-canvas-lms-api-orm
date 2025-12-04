<?php

namespace CanvasApiLibrary\Caching\Providers;

use CanvasApiLibrary\Caching\CacheRules\UndefinedCacherule;
use CanvasApiLibrary\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Caching\Utility\CacheRule;
use CanvasApiLibrary\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Providers\SubmissionProvider;
use CanvasApiLibrary\Providers\Generated\Traits\SubmissionProviderProperties;
use CanvasApiLibrary\Providers\Interfaces\SubmissionProviderInterface;

class SubmissionProviderCached implements SubmissionProviderInterface{

    use SubmissionProviderProperties;
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

    public function getSubmissionsInAssignment(\CanvasApiLibrary\Models\Assignment $assignment, ?UserProviderInterface $userProvider = null): array{
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

    public function populateSubmission(\CanvasApiLibrary\Models\Submission $submission): \CanvasApiLibrary\Models\Submission{
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
