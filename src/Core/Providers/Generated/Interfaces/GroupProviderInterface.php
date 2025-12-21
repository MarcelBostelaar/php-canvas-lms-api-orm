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
interface GroupProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
    /**
	 * @param Group[] $groups
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<Group[]>|TUnauthorizedResult
    */
    public function populateGroups(array $groups) : mixed;

    /**
	 * @param GroupCategory $groupCategory
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<Group[]>|TUnauthorizedResult
    */
    public function getAllGroupsInGroupCategory(GroupCategory $groupCategory) : mixed;

    /**
	 * @param Group $group
	 * @return TErrorResult|TNotFoundResult|TSuccessResult<Group>|TUnauthorizedResult
    */
    public function populateGroup(Group $group) : mixed;

}
