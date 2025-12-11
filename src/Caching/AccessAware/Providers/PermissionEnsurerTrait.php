<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Services\PermissionEnsurer;
use CanvasApiLibrary\Core\Providers\Interfaces\CourseProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;

trait PermissionEnsurerTrait{
    private ?PermissionEnsurer $permissionEnsurer;
    public function bindPreCacheMethod(PermissionEnsurer $ensurer){
        $this->permissionEnsurer = $ensurer;
    }
}