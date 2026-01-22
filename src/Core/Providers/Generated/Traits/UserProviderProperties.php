<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Core\Providers\Generated\Traits;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
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

trait UserProviderProperties{
    
    
    abstract public function getUsersInGroup(GroupStub $group, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function getUsersInSection(SectionStub $section, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function getUsersInCourse(CourseStub $course, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function populateUser(UserStub $user, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    /**
     * Summary of getUsersInGroups 
     * This is a plural version of getUsersInGroup 
      * @param GroupStub[] $groups
 * @param bool $skipCache
 * @param bool $doNotCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<GroupStub, User[]>>|UnauthorizedResult     */
    public function getUsersInGroups(array $groups, bool $skipCache = false, bool $doNotCache = false): SuccessResult|ErrorResult|NotFoundResult|UnauthorizedResult {
        $lookup = new Lookup();
        foreach($groups as $x){
            $result = $this->getUsersInGroup($x, $skipCache, $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $lookup->add($x, $result->value);
        }
        return new SuccessResult($lookup);
    }
    /**
     * Summary of getUsersInSections 
     * This is a plural version of getUsersInSection 
      * @param SectionStub[] $sections
 * @param ?string $enrollmentRoleFilter
 * @param bool $skipCache
 * @param bool $doNotCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<SectionStub, User[]>>|UnauthorizedResult     */
    public function getUsersInSections(array $sections, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false): SuccessResult|ErrorResult|NotFoundResult|UnauthorizedResult {
        $lookup = new Lookup();
        foreach($sections as $x){
            $result = $this->getUsersInSection($x, $enrollmentRoleFilter, $skipCache, $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $lookup->add($x, $result->value);
        }
        return new SuccessResult($lookup);
    }
    /**
     * Summary of getUsersInCourses 
     * This is a plural version of getUsersInCourse 
      * @param CourseStub[] $courses
 * @param ?string $enrollmentRoleFilter
 * @param bool $skipCache
 * @param bool $doNotCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<CourseStub, User[]>>|UnauthorizedResult     */
    public function getUsersInCourses(array $courses, ?string $enrollmentRoleFilter, bool $skipCache = false, bool $doNotCache = false): SuccessResult|ErrorResult|NotFoundResult|UnauthorizedResult {
        $lookup = new Lookup();
        foreach($courses as $x){
            $result = $this->getUsersInCourse($x, $enrollmentRoleFilter, $skipCache, $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $lookup->add($x, $result->value);
        }
        return new SuccessResult($lookup);
    }
    /**
     * Summary of populateUsers
 
     * This is a plural version of populateUser
 
	 * @param UserStub[] $users
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
     */
    public function populateUsers(array $users, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $results = [];
        foreach($users as $item){
            $result = $this->populateUser($item, $skipCache,  $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
        }
        return new SuccessResult($results);
    }
    
}
