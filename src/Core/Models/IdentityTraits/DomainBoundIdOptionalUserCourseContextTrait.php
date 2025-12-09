<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits;

use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalCourseContextTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalUserContextTrait;

trait DomainBoundIdOptionalUserCourseContextTrait{
    use DomainBoundIdTrait{
        DomainBoundIdTrait::initIdentityTraits as protected wrappedInitIdentityTraits;
    }
    use OptionalCourseContextTrait;
    use OptionalUserContextTrait;

    protected function initIdentityTraits(){
        $this->wrappedInitIdentityTraits();
        $this->initializeOptionalCourseContext();
        $this->initializeOptionalUserContext();
    }
}