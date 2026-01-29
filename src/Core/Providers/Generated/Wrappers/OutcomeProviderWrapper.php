<?php
//Auto-generated file, changes will be lost
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;
use Closure;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\AssignmentStub;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\Group;
use CanvasApiLibrary\Core\Models\GroupCategory;
use CanvasApiLibrary\Core\Models\GroupCategoryStub;
use CanvasApiLibrary\Core\Models\GroupStub;
use CanvasApiLibrary\Core\Models\Outcome;
use CanvasApiLibrary\Core\Models\OutcomeResult;
use CanvasApiLibrary\Core\Models\OutcomeResultStub;
use CanvasApiLibrary\Core\Models\OutcomeStub;
use CanvasApiLibrary\Core\Models\Outcomegroup;
use CanvasApiLibrary\Core\Models\OutcomegroupStub;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\SectionStub;
use CanvasApiLibrary\Core\Models\Submission;
use CanvasApiLibrary\Core\Models\SubmissionComment;
use CanvasApiLibrary\Core\Models\SubmissionCommentStub;
use CanvasApiLibrary\Core\Models\SubmissionStub;
use CanvasApiLibrary\Core\Models\User;
use CanvasApiLibrary\Core\Models\UserDisplay;
use CanvasApiLibrary\Core\Models\UserStub;

/**
 * @template TSuccessResult Wrapped success type
 * @template TSuccessResult2 Returned success type
 * @template TUnauthorizedResult Wrapped type of value that an unauthorized result will emit
 * @template TUnauthorizedResult2 Returned type of value that an unauthorized result will emit
 * @template TNotFoundResult Wrapped type of value that a not found result will emit
 * @template TNotFoundResult2 Returned type of value that a not found result will emit
 * @template TErrorResult Wrapped type of value that any other error result will emit
 * @template TErrorResult2 Returned type of value that any other error result will emit
 * @implements OutcomeProviderInterface<TSuccessResult2,TErrorResult2,TNotFoundResult2,TUnauthorizedResult2>
 */
class OutcomeProviderWrapper implements OutcomeProviderInterface {

    /**
     * Summary of __construct
     * @param OutcomeProviderInterface<TSuccessResult,TErrorResult,TNotFoundResult,TUnauthorizedResult> $innerProvider
     * @param Closure(TSuccessResult|TErrorResult|TNotFoundResult|TUnauthorizedResult) : (TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2) $resultProcessor
     */
    public function __construct(
        private OutcomeProviderInterface $innerProvider,
        private Closure $resultProcessor){
    }

    public function getClientID(): string {
        return $this->innerProvider->getClientID();
    }

    /**
     * Summary of handleResults
     * @template newSuccessT
     * @template newUnauthorizedT
     * @template newNotFoundT
     * @template newErrorT
     * @param Closure(TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2) : (newSuccessT|newErrorT|newNotFoundT|newUnauthorizedT) $processor
     * @return OutcomeProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): OutcomeProviderInterface {
        $previousProcessor = $this->resultProcessor ?? fn($x) => $x;
        return new OutcomeProviderWrapper( $this->innerProvider, fn($x) => $processor($previousProcessor($x)));
    }

    public function HandleEmitted(mixed $data, array $context): void {
        $this->innerProvider->HandleEmitted($data, $context);
    }

    /**
	 * @param OutcomeStub[] $outcomes
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function populateOutcomes(array $outcomes, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->populateOutcomes($outcomes, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param OutcomegroupStub[] $outcomegroups
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function getOutcomesInOutcomegroups(array $outcomegroups, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->getOutcomesInOutcomegroups($outcomegroups, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param OutcomeStub $outcome
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function populateOutcome(OutcomeStub $outcome, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->populateOutcome($outcome, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param OutcomegroupStub $outcomeGroup
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function getOutcomesInOutcomegroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->getOutcomesInOutcomegroup($outcomeGroup, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

}
