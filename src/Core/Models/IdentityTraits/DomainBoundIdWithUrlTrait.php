<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits;

use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\DomainIdentityTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\IdentityBoiletplateTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\NumberIdentityTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\UrlIdentityTrait;

/**
 * Identity trait for models bound to: domain & id, with an included url that can be used to fetch the model
 */
trait DomainBoundIdWithUrlTrait{
    use IdentityBoiletplateTrait;
    use NumberIdentityTrait;
    use DomainIdentityTrait;
    use UrlIdentityTrait;

    protected function initIdentityTraits(){
        $this->initializeNumberIdentity();
        $this->initializeDomainIdentity();
        $this->initializeUrlIdentity();
    }
}
