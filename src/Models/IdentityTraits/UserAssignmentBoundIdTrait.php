<?php

namespace CanvasApiLibrary\Models\IdentityTraits;

use CanvasApiLibrary\Models\IdentityTraits\Base\AssignmentIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\CourseIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\DomainIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\IdentityBoiletplateTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\NumberIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\UserIdentityTrait;

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
    

    private function initIdentityTraits(){
        $this->initializeNumberIdentity();
        $this->initializeDomainIdentity();
        $this->initializeCourseIdentity();
        $this->initializeUserIdentity();
        $this->initializeAssignmentIdentity();
    }
}
