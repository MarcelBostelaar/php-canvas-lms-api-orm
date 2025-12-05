<?php
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Core\Models\Assignment;

interface AssignmentProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
    /**
    * @param Assignment[] $assignments
    * @return Assignment[]
    */
    public function populateAssignments(array $assignments) : array;

    /**
    * @param Assignment $assignment
    * @return Assignment
    */
    public function populateAssignment(Assignment $assignment) : Assignment;

}
