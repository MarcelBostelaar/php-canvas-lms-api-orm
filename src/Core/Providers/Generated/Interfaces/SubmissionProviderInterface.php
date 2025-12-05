<?php
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Core\Models\Submission;
use CanvasApiLibrary\Core\Models\Assignment;

interface SubmissionProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
    /**
    * @param Submission[] $submissions
    * @return Submission[]
    */
    public function populateSubmissions(array $submissions) : array;

    /**
    * @param Assignment[] $assignments	 * @param ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider
    * @return Lookup<Assignment, Submission>
    */
    public function getSubmissionsInAssignments(array $assignments, ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider) : Lookup;

    /**
    * @param Assignment $assignment	 * @param ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider
    * @return mixed
    */
    public function getSubmissionsInAssignment(Assignment $assignment, ?CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface $userProvider) : mixed;

    /**
    * @param Submission $submission
    * @return Submission
    */
    public function populateSubmission(Submission $submission) : Submission;

}
