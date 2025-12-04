<?php

namespace CanvasApiLibrary\Models\IdentityTraits;

use CanvasApiLibrary\Models\IdentityTraits\Atomic\AssignmentIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\CourseIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\DomainIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\IdentityBoiletplateTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\NumberIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\UserIdentityTrait;

/**
 * Identity trait for models bound to: A user and an assignment -> course -> domain & id
 */
trait UserAssignmentBoundIdTrait{
    use IdentityBoiletplateTrait;
    use NumberIdentityTrait;
    use DomainIdentityTrait;
    use CourseIdentityTrait;
    use UserIdentityTrait;
    use AssignmentIdentityTrait;
    

    protected function initIdentityTraits(){
        $this->initializeNumberIdentity();
        $this->initializeDomainIdentity();
        $this->initializeCourseIdentity();
        $this->initializeUserIdentity();
        $this->initializeAssignmentIdentity();
    }
}
