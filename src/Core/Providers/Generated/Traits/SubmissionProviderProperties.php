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

trait SubmissionProviderProperties{
    
    
    abstract public function getSubmissionsInAssignment(AssignmentStub $assignment, ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    abstract public function populateSubmission(SubmissionStub $submission, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    /**
     * Summary of getSubmissionsInAssignments     * This is a plural version of getSubmissionsInAssignment      * @param AssignmentStub[] $assignments
 * @param ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider
 * @param bool $skipCache
 * @param bool $doNotCache
 * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<AssignmentStub, Submission[]>>|UnauthorizedResult     */
    public function getSubmissionsInAssignments(array $assignments, ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider, bool $skipCache = false, bool $doNotCache = false): Lookup{
        $lookup = new Lookup();
        foreach($assignments as $x){
            $lookup->add($x, $this->getSubmissionsInAssignment($x));
        }
        return $lookup;
    }
    /**
     * Summary of populateSubmissions
     * This is a plural version of populateSubmission
	 * @param SubmissionStub[] $submissions
	 * @param bool $skipCache
	 * @param bool $doNotCache
	 * @return ErrorResult|NotFoundResult|SuccessResult<Submission[]>|UnauthorizedResult
     */
    public function populateSubmissions(array $submissions, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $results = [];
        foreach($submissions as $item){
            $result = $this->populateSubmission($item, $skipCache,  $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
        }
        return new SuccessResult($results);
    }
    
}
