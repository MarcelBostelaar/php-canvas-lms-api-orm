<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits;

use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalCourseContextTrait;

trait DomainBoundIdOptionalCourseContextTrait{
    use DomainBoundIdTrait{
        DomainBoundIdTrait::initIdentityTraits as protected wrappedInitIdentityTraits;
    }
    use OptionalCourseContextTrait;

    protected function initIdentityTraits(){
        $this->wrappedInitIdentityTraits();
        $this->initializeOptionalCourseContext();
    }
    public function isRegularModel() : bool{
        return true;
    }
    public function isUrlModel() : bool{
        return false;
    }
}