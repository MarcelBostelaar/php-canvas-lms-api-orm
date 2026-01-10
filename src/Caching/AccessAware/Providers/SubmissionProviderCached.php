<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Core\Models\AssignmentStub;
use CanvasApiLibrary\Core\Models\SubmissionStub;
use CanvasApiLibrary\Core\Models\Submission;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\SubmissionProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SubmissionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SubmissionProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\SubmissionWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;


/**
 * @implements SubmissionProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class SubmissionProviderCached implements SubmissionProviderInterface{

    use SubmissionProviderProperties;
    use PermissionEnsurerTrait;
    use SubmissionWrapperTrait;
    use CacheHelperTrait;

    public function __construct(
        private readonly SubmissionProvider $wrapped,
        private readonly CacheProviderInterface $cache,
        public readonly int $ttl,
        private readonly PermissionsHandlerInterface $permissionHandler
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getClientID(): String{
        return $this->wrapped->getClientID();
    }

    /**
	 * @param AssignmentStub $assignment
	 * @param ?UserProviderInterface $userProvider
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<Submission[]>|UnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getSubmissionsInAssignment(AssignmentStub $assignment, ?UserProviderInterface $userProvider, bool $skipCache = false, bool $doNotCache = false) : mixed{
        return $this->userInCourseScopedCollectionValue(
            "getSubmissionsInAssignment" . AssignmentStub::fromStub($assignment)->getResourceKey(),
            fn() => $this->wrapped->getSubmissionsInAssignment($assignment, $userProvider, $skipCache, $doNotCache),
            fn(Submission $x) => [$this->permissionHandler::domainCourseUserPermission($x->course, $x->user)],
            $skipCache,
            $doNotCache,
            $assignment->course
        );
    }

    /**
	 * @param SubmissionStub $submission
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<Submission>|UnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function populateSubmission(SubmissionStub $submission, bool $skipCache = false, bool $doNotCache = false) : mixed{
        return $this->userInCourseSingleValue(
            Submission::fromStub($submission)->getResourceKey(),
            fn() => $this->wrapped->populateSubmission($submission, $skipCache, $doNotCache),
            $submission->user,
            $submission->course,
            $skipCache,
            $doNotCache
        );
    }
}
