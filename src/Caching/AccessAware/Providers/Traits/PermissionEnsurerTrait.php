<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers\Traits;

use CanvasApiLibrary\Caching\AccessAware\Services\PermissionEnsurer;

trait PermissionEnsurerTrait{
    private ?PermissionEnsurer $permissionEnsurer;
    public function bindEnsurer(PermissionEnsurer $ensurer){
        $this->permissionEnsurer = $ensurer;
    }
}