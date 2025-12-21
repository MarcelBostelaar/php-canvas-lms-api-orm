<?php
//Auto-generated file, changes will be lost
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;
use Closure;

use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\Group;
use CanvasApiLibrary\Core\Models\GroupCategory;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\Submission;
use CanvasApiLibrary\Core\Models\SubmissionComment;
use CanvasApiLibrary\Core\Models\User;
use CanvasApiLibrary\Core\Models\UserDisplay;
use CanvasApiLibrary\Core\Models\UserStub;

/**
 * @template TSuccessResult The type that a successful result will emit, which itself should be a class with a generic type.
 * @template TUnauthorizedResult Type of value that an unauthorized result will emit
 * @template TNotFoundResult Type of value that a not found result will emit
 * @template TErrorResult Type of value that any other error result will emit
 */
interface AssignmentProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;

    /**
     * Summary of handleResults
     * @template newSuccessT
     * @template newUnauthorizedT
     * @template newNotFoundT
     * @template newErrorT
     * @param Closure(TSuccessResult|TErrorResult|TNotFoundResult|TUnauthorizedResult) : (newSuccessT|newErrorT|newNotFoundT|newUnauthorizedT) $processor
     * @return AssignmentProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): AssignmentProviderInterface;
    /**
	 * @param Assignment[] $assignments
	 * @param bool $skipCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<Assignment[]>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function populateAssignments(array $assignments, bool $skipCache = false) : mixed;

    /**
	 * @param Assignment $assignment
	 * @param bool $skipCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<Assignment>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function populateAssignment(Assignment $assignment, bool $skipCache = false) : mixed;

}
