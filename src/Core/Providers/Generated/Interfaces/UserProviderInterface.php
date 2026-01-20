<?php
//Auto-generated file, changes will be lost
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;
use Closure;

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
use CanvasApiLibrary\Core\Models\OutcomeGroup;
use CanvasApiLibrary\Core\Models\OutcomeGroupStub;
use CanvasApiLibrary\Core\Models\OutcomeResult;
use CanvasApiLibrary\Core\Models\OutcomeResultStub;
use CanvasApiLibrary\Core\Models\OutcomeStub;
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
 * @template TSuccessResult The type that a successful result will emit, which itself should be a class with a generic type.
 * @template TUnauthorizedResult Type of value that an unauthorized result will emit
 * @template TNotFoundResult Type of value that a not found result will emit
 * @template TErrorResult Type of value that any other error result will emit
 */
interface UserProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;

    /**
     * Summary of handleResults
     * @template newSuccessT
     * @template newUnauthorizedT
     * @template newNotFoundT
     * @template newErrorT
     * @param Closure(TSuccessResult|TErrorResult|TNotFoundResult|TUnauthorizedResult) : (newSuccessT|newErrorT|newNotFoundT|newUnauthorizedT) $processor
     * @return UserProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): UserProviderInterface;
    /**
	 * @param GroupStub[] $groups
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<Lookup<GroupStub, User[]>>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInGroups(array $groups, bool $skipCache = false, bool $doNotCache = false) : mixed;

    /**
	 * @param SectionStub[] $sections
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<Lookup<SectionStub, User[]>>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInSections(array $sections, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed;

    /**
	 * @param CourseStub[] $courses
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<Lookup<CourseStub, User[]>>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInCourses(array $courses, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed;

    /**
	 * @param UserStub[] $users
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User[]>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function populateUsers(array $users, bool $skipCache = false, bool $doNotCache = false) : mixed;

    /**
	 * @param GroupStub $group
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User[]>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInGroup(GroupStub $group, bool $skipCache = false, bool $doNotCache = false) : mixed;

    /**
	 * @param SectionStub $section
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User[]>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInSection(SectionStub $section, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed;

    /**
	 * @param CourseStub $course
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User[]>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInCourse(CourseStub $course, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed;

    /**
	 * @param UserStub $user
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User>|TUnauthorizedResult
     * @phpstan-ignore return.unresolvableType
    */
    public function populateUser(UserStub $user, bool $skipCache = false, bool $doNotCache = false) : mixed;

}
