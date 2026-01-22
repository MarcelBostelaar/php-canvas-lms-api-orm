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

trait OutcomeProviderProperties{
    
    
    abstract public function populateOutcome(OutcomeStub $outcome, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function getOutcomesInOutcomegroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    /**
     * Summary of populateOutcomes
 
     * This is a plural version of populateOutcome
 
	 * @param OutcomeStub[] $outcomes
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<Outcome[]>|UnauthorizedResult
     */
    public function populateOutcomes(array $outcomes, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $results = [];
        foreach($outcomes as $item){
            $result = $this->populateOutcome($item, $skipCache,  $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
        }
        return new SuccessResult($results);
    }
        /**
     * Summary of getOutcomesInOutcomegroups 
     * This is a plural version of getOutcomesInOutcomegroup 
      * @param OutcomegroupStub[] $outcomegroups
 * @param bool $skipCache
 * @param bool $doNotCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<OutcomegroupStub, Outcome[]>>|UnauthorizedResult     */
    public function getOutcomesInOutcomegroups(array $outcomegroups, bool $skipCache = false, bool $doNotCache = false): SuccessResult|ErrorResult|NotFoundResult|UnauthorizedResult {
        $lookup = new Lookup();
        foreach($outcomegroups as $x){
            $result = $this->getOutcomesInOutcomegroup($x, $skipCache, $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $lookup->add($x, $result->value);
        }
        return new SuccessResult($lookup);
    }

}
