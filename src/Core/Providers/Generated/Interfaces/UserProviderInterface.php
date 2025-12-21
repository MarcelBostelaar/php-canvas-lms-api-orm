<?php
//Auto-generated file, changes will be lost
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

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
interface UserProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
    /**
	 * @param Group $group
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User[]>|TUnauthorizedResult
    */
    public function getUsersInGroup(Group $group) : mixed;

    /**
	 * @param Section $section
	 * @param ?string $enrollmentRoleFilter
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User[]>|TUnauthorizedResult
    */
    public function getUsersInSection(Section $section, ?string $enrollmentRoleFilter) : mixed;

    /**
	 * @param Course $course
	 * @param ?string $enrollmentRoleFilter
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User[]>|TUnauthorizedResult
    */
    public function getUsersInCourse(Course $course, ?string $enrollmentRoleFilter) : mixed;

    /**
	 * @param Domain $domain
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User>|TUnauthorizedResult
    */
    public function getUserSelfInfo(Domain $domain) : mixed;

    /**
	 * @param UserStub $user
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<User>|TUnauthorizedResult
    */
    public function populateUser(UserStub $user) : mixed;

}
