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

trait GroupProviderProperties{
    
    
    abstract public function getAllGroupsInGroupCategory(GroupCategoryStub $groupCategory, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function populateGroup(GroupStub $group, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    /**
     * Summary of getAllGroupsInGroupCategories 
     * This is a plural version of getAllGroupsInGroupCategory 
      * @param GroupCategoryStub[] $groupCategories
 * @param bool $skipCache
 * @param bool $doNotCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<GroupCategoryStub, Group>>|UnauthorizedResult     */
    public function getAllGroupsInGroupCategories(array $groupCategories, bool $skipCache = false, bool $doNotCache = false): SuccessResult|ErrorResult|NotFoundResult|UnauthorizedResult {
        $lookup = new Lookup();
        foreach($groupCategories as $x){
            $result = $this->getAllGroupsInGroupCategory($x, $skipCache, $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            foreach($result->value as $y){
                $lookup->add($x, $y);
            }
        }
        return new SuccessResult($lookup);
    }
    /**
     * Summary of populateGroups
 
     * This is a plural version of populateGroup
 
	 * @param GroupStub[] $groups
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<Group[]>|UnauthorizedResult
     */
    public function populateGroups(array $groups, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $results = [];
        foreach($groups as $item){
            $result = $this->populateGroup($item, $skipCache,  $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
        }
        return new SuccessResult($results);
    }
    
}
