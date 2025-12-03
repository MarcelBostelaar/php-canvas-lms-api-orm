<?php

namespace CanvasApiLibrary\Models\IdentityTraits;

use CanvasApiLibrary\Models\IdentityTraits\Atomic\DomainIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\IdentityBoiletplateTrait;
use CanvasApiLibrary\Models\IdentityTraits\Atomic\NumberIdentityTrait;

/**
 * Identity trait for models bound to: domain & id
 */
trait DomainBoundIdTrait{
    use NumberIdentityTrait;
    use DomainIdentityTrait;

    protected function initIdentityTraits(){
        $this->initializeNumberIdentity();
        $this->initializeDomainIdentity();
    }
}
