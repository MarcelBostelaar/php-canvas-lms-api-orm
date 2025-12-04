<?php

namespace CanvasApiLibrary\Models\IdentityTraits;

use CanvasApiLibrary\Models\IdentityTraits\Atomic\CourseIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\DomainIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\IdentityBoiletplateTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\NumberIdentityTrait;

/**
 * Identity trait for models bound to: course -> domain & id
 */
trait CourseBoundIdTrait{
    use IdentityBoiletplateTrait;
    use NumberIdentityTrait;
    use DomainIdentityTrait;
    use CourseIdentityTrait;

    protected function initIdentityTraits(){
        $this->initializeNumberIdentity();
        $this->initializeDomainIdentity();
        $this->initializeCourseIdentity();
    }
}
