<?php
namespace CanvasApiLibrary\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Models\Submission;
use CanvasApiLibrary\Models\Assignment;

interface SubmissionProviderInterface extends HandleEmittedInterface{

    /**
    * @param Submission[] $submissions
    * @return Submission[]
    */
    public function populateSubmissions(array $submissions) : array;

    /**
    * @param Assignment[] $assignments	 * @param ?CanvasApiLibrary\Providers\UserProvider $userProvider
    * @return Lookup<Assignment, Submission>
    */
    public function getSubmissionsInAssignments(array $assignments, ?CanvasApiLibrary\Providers\UserProvider $userProvider) : Lookup;

    /**
    * @param Assignment $assignment	 * @param ?CanvasApiLibrary\Providers\UserProvider $userProvider
    * @return mixed
    */
    public function getSubmissionsInAssignment(Assignment $assignment, ?CanvasApiLibrary\Providers\UserProvider $userProvider) : mixed;

    /**
    * @param Submission $submission
    * @return Submission
    */
    public function populateSubmission(Submission $submission) : Submission;

}
