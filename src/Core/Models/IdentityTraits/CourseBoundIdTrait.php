<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits;

use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\CourseIdentityTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\DomainIdentityTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\IdentityBoiletplateTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\NumberIdentityTrait;

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
