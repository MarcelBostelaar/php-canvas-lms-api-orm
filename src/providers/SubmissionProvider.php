<?php

namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Submission;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\UserProvider;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;



/**
 * Provider for Canvas API group operations
 * 
 * @method Lookup<Models\Assignment, Models\Submission> getSubmissionsForAssignments() Virtual method to get all groups in group categories
 */
class SubmissionProvider extends AbstractProvider{
    use SubmissionProviderProperties;

    /**
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @param \CanvasApiLibrary\Models\Course $course
     * @param \CanvasApiLibrary\Models\Assignment $assignment
     * @param mixed $userProvider Optional, if provided will be used to pre-fetch user info and emit to user provider
     * @return Submission[]
     */
    protected function getSubmissionsForAssignment(Domain $domain, Course $course, Assignment $assignment, ?UserProvider $userProvider = null) : array{
        $postfix = $userProvider ? "?include[]=user" : "";
        return $this->Get($domain, "courses/$course->id/assignments/$assignment->id/submissions$postfix",
            $userProvider ? [["user", $userProvider]] : []
        );
    }

    protected function populateModel(Models\Domain $domain, $model, mixed $data): Models\Utility\AbstractCanvasPopulatedModel{
        //todo
    }

    public function populateSubmission(Submission $submission){
        //todo
    }
}