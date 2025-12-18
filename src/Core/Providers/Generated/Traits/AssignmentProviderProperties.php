<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Core\Providers\Generated\Traits;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Models\Assignment;

trait AssignmentProviderProperties{
    
    
    
    abstract public function populateAssignment(Assignment $assignment);
    
    /**
    * Plural version of populateAssignment
    * @param Assignment[] $assignments
    * @return Assignment[]

    */
    public function populateAssignments(array $assignments) : array{
        return array_map(fn($x) => $this->populateAssignment($x), $assignments);
    }
    
    }
