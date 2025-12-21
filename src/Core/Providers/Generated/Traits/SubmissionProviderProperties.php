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

trait SubmissionProviderProperties{
    
    
    abstract public function populateSubmission(Submission $submission) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult;
    /**
     * Summary of populateSubmissions
     * This is a plural version of populateSubmission
	 * @param Submission[] $submissions
	 * @return ErrorResult|NotFoundResult|SuccessResult<Submission[]>|UnauthorizedResult
     */
    public function populateSubmissions(array $submissions): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $results = [];
        foreach($submissions as $item){
            $result = $this->populateSubmission($item);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
        }
        return new SuccessResult($results);
    }
    
}
