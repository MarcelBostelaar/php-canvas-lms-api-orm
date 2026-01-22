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

trait OutcomegroupProviderProperties{
    
    
    abstract public function populateOutcomegroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function getOutcomegroupsInCourse(CourseStub $course, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    /**
     * Summary of populateOutcomesgroup
 
     * This is a plural version of populateOutcomegroup
 
	 * @param OutcomegroupStub[] $outcomes
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<Outcomegroup[]>|UnauthorizedResult
     */
    public function populateOutcomesgroup(array $outcomes, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $results = [];
        foreach($outcomes as $item){
            $result = $this->populateOutcomegroup($item, $skipCache,  $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
        }
        return new SuccessResult($results);
    }
        /**
     * Summary of populateOutcomegroups
 
     * This is a plural version of populateOutcomegroup
 
	 * @param OutcomegroupStub[] $outcomegroups
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<Outcomegroup[]>|UnauthorizedResult
     */
    public function populateOutcomegroups(array $outcomegroups, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $results = [];
        foreach($outcomegroups as $item){
            $result = $this->populateOutcomegroup($item, $skipCache,  $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
        }
        return new SuccessResult($results);
    }
        /**
     * Summary of getOutcomegroupsInCourses 
     * This is a plural version of getOutcomegroupsInCourse 
      * @param CourseStub[] $courses
 * @param bool $skipCache
 * @param bool $doNotCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<CourseStub, Outcomegroup[]>>|UnauthorizedResult     */
    public function getOutcomegroupsInCourses(array $courses, bool $skipCache = false, bool $doNotCache = false): Lookup{
        $lookup = new Lookup();
        foreach($courses as $x){
            $lookup->add($x, $this->getOutcomegroupsInCourse($x, $skipCache, $doNotCache));
        }
        return $lookup;
    }

}
