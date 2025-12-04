<?php
namespace CanvasApiLibrary\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Models\Assignment;

interface AssignmentProviderInterface extends HandleEmittedInterface{

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
