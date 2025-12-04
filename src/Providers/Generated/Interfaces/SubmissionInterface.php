<?php
namespace CanvasApiLibrary\Providers\Interfaces;

use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Models\Submission;
use CanvasApiLibrary\Models\Assignment;

interface SubmissionInterface{

    /**
    * @param Submission[] $submissions
    * @return Submission[]
    */
    public function populateSubmissions(array $submissions) : array;

    /**
    * @param Assignment $assignment	 * @param ?CanvasApiLibrary\Providers\UserProvider $userProvider
    * @return mixed
    */
    public function getSubmissionsForAssignment(Assignment $assignment, ?CanvasApiLibrary\Providers\UserProvider $userProvider) : mixed;

    /**
    * @param Submission $submission
    * @return Submission
    */
    public function populateSubmission(Submission $submission) : Submission;

}
