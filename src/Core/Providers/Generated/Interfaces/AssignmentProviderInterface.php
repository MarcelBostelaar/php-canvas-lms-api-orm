<?php
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Core\Models\Assignment;

/**
 * @template TSuccessResult
 * @template TUnauthorizedResult
 * @template TNotFoundResult
 * @template TErrorResult
 */
interface AssignmentProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
    /**
    * @param Assignment[] $assignments
    * @return array<TSuccessResult<Assignment>|TUnauthorizedResult|TNotFoundResult|TErrorResult>
    */
    public function populateAssignments(array $assignments) : array;

    /**
    * @param Assignment $assignment
    * @return TSuccessResult<Assignment>|TUnauthorizedResult|TNotFoundResult|TErrorResult
    */
    public function populateAssignment(Assignment $assignment);

}
