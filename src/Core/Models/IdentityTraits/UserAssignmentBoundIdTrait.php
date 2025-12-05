<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits;

use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\AssignmentIdentityTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\CourseIdentityTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\DomainIdentityTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\IdentityBoiletplateTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\NumberIdentityTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\UserIdentityTrait;

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
