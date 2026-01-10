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
 * @implements UserProviderInterface<TSuccessResult2,TErrorResult2,TNotFoundResult2,TUnauthorizedResult2>
 */
class UserProviderWrapper implements UserProviderInterface {

    /**
     * Summary of __construct
     * @param UserProviderInterface<TSuccessResult,TErrorResult,TNotFoundResult,TUnauthorizedResult> $innerProvider
     * @param Closure(TSuccessResult|TErrorResult|TNotFoundResult|TUnauthorizedResult) : (TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2) $resultProcessor
     */
    public function __construct(
        private UserProviderInterface $innerProvider,
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
     * @return UserProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): UserProviderInterface {
        $previousProcessor = $this->resultProcessor ?? fn($x) => $x;
        return new UserProviderWrapper( $this->innerProvider, fn($x) => $processor($previousProcessor($x)));
    }

    public function HandleEmitted(mixed $data, array $context): void {
        $this->innerProvider->HandleEmitted($data, $context);
    }

    /**
	 * @param GroupStub[] $groups
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInGroups(array $groups, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->getUsersInGroups($groups, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param SectionStub[] $sections
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInSections(array $sections, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->getUsersInSections($sections, $enrollmentRoleFilter, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param CourseStub[] $courses
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInCourses(array $courses, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->getUsersInCourses($courses, $enrollmentRoleFilter, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param UserStub[] $users
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function populateUsers(array $users, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->populateUsers($users, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param GroupStub $group
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInGroup(GroupStub $group, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->getUsersInGroup($group, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param SectionStub $section
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInSection(SectionStub $section, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->getUsersInSection($section, $enrollmentRoleFilter, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param CourseStub $course
	 * @param ?string $enrollmentRoleFilter
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function getUsersInCourse(CourseStub $course, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->getUsersInCourse($course, $enrollmentRoleFilter, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

    /**
	 * @param UserStub $user
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2
     * @phpstan-ignore return.unresolvableType
    */
    public function populateUser(UserStub $user, bool $skipCache = false, bool $doNotCache = false) : mixed{
        $value = $this->innerProvider->populateUser($user, $skipCache, $doNotCache);
        return ($this->resultProcessor)($value);
    }

}
