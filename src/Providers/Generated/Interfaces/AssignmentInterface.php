<?php
namespace CanvasApiLibrary\Providers\Interfaces;

use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Models\Assignment;

interface AssignmentInterface{

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
