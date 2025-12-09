<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Core\Providers\Generated\Traits;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\Submission;

trait SubmissionProviderProperties{
    
    
    
    abstract public function populateSubmission(Submission$submission);
    
    /**
    * Plural version of populateSubmission
    * @param Submission[] $submissions
    * @return Submission[]

    */
    public function populateSubmissions(array $submissions) : array{
        return array_map(fn($x) => $this->populateSubmission($x), $submissions);
    }
    
    
    abstract public function getSubmissionsInAssignment(Assignment $assignment, ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider) : array;
    
    /**
     * Summary of getSubmissionsInAssignments
     * @param Assignment[] $assignments
     * @return Lookup<Assignment, Assignment>
     */
    public function getSubmissionsInAssignments(array $assignments, ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider): Lookup{
        $lookup = new Lookup();
        foreach($assignments as $assignment){
            $lookup->add($assignment, $this->getSubmissionsInAssignment($assignment, $userProvider));
        }
        return $lookup;
    }
}
