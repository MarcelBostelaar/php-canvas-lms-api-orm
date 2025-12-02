<?php

namespace CanvasApiLibrary\Models\IdentityTraits;

use CanvasApiLibrary\Models\IdentityTraits\Base\DomainIdentityTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\IdentityBoiletplateTrait;
use CanvasApiLibrary\Models\IdentityTraits\Base\NumberIdentityTrait;

/**
 * Identity trait for models bound to: domain & id
 */
trait DomainBoundIdTrait{
    use IdentityBoiletplateTrait;
    use NumberIdentityTrait;
    use DomainIdentityTrait;

    private function initIdentityTraits(){
        $this->initializeNumberIdentity();
        $this->initializeDomainIdentity();
    }
}
