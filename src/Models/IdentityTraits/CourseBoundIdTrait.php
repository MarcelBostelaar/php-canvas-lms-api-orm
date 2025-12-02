<?php

namespace CanvasApiLibrary\Models\IdentityTraits;

use CanvasApiLibrary\Models\IdentityTraits\Base\CourseIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\DomainIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\IdentityBoiletplateTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\NumberIdentityTrait;

/**
 * Identity trait for models bound to: course -> domain & id
 */
trait CourseBoundIdTrait{
    use IdentityBoiletplateTrait;
    use NumberIdentityTrait;
    use DomainIdentityTrait;
    use CourseIdentityTrait;

    private function initIdentityTraits(){
        $this->initializeNumberIdentity();
        $this->initializeDomainIdentity();
        $this->initializeCourseIdentity();
    }
}
