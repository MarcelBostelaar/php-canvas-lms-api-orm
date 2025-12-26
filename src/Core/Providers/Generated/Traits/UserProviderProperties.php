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
    
    
    abstract public function getUsersInGroup(GroupStub $group, bool $skipCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function getUsersInSection(SectionStub $section, ?string $enrollmentRoleFilter, bool $skipCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function getUsersInCourse(CourseStub $course, ?string $enrollmentRoleFilter, bool $skipCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function getUsersInDomain(Domain $domain, bool $skipCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function populateUser(UserStub $user, bool $skipCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    /**
     * Summary of getUsersInGroups     * This is a plural version of getUsersInGroup      * @param GroupStub[] $groups
 * @param bool $skipCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<GroupStub, User[]>>|UnauthorizedResult     */
    public function getUsersInGroups(array $groups, bool $skipCache = false): Lookup{
        $lookup = new Lookup();
        foreach($groups as $x){
            $lookup->add($x, $this->getUsersInGroup($x));
        }
        return $lookup;
    }
    /**
     * Summary of getUsersInSections     * This is a plural version of getUsersInSection      * @param SectionStub[] $sections
 * @param ?string $enrollmentRoleFilter
 * @param bool $skipCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<SectionStub, User[]>>|UnauthorizedResult     */
    public function getUsersInSections(array $sections, ?string $enrollmentRoleFilter, bool $skipCache = false): Lookup{
        $lookup = new Lookup();
        foreach($sections as $x){
            $lookup->add($x, $this->getUsersInSection($x));
        }
        return $lookup;
    }
    /**
     * Summary of getUsersInCourses     * This is a plural version of getUsersInCourse      * @param CourseStub[] $courses
 * @param ?string $enrollmentRoleFilter
 * @param bool $skipCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<CourseStub, User[]>>|UnauthorizedResult     */
    public function getUsersInCourses(array $courses, ?string $enrollmentRoleFilter, bool $skipCache = false): Lookup{
        $lookup = new Lookup();
        foreach($courses as $x){
            $lookup->add($x, $this->getUsersInCourse($x));
        }
        return $lookup;
    }
    /**
     * Summary of getUsersInDomains     * This is a plural version of getUsersInDomain      * @param Domain[] $domains
 * @param bool $skipCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<Domain, User[]>>|UnauthorizedResult     */
    public function getUsersInDomains(array $domains, bool $skipCache = false): Lookup{
        $lookup = new Lookup();
        foreach($domains as $x){
            $lookup->add($x, $this->getUsersInDomain($x));
        }
        return $lookup;
    }
    /**
     * Summary of populateUsers
     * This is a plural version of populateUser
	 * @param UserStub[] $users
	 * @param bool $skipCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
     */
    public function populateUsers(array $users, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $results = [];
        foreach($users as $item){
            $result = $this->populateUser($item, $skipCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
        }
        return new SuccessResult($results);
    }
    
}
