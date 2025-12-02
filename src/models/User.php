<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\IdentityTraits\DomainBoundIdTrait;
use CanvasApiLibrary\Models\Generated\UserProperties;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

class User extends AbstractCanvasPopulatedModel{
    use UserProperties;
    use DomainBoundIdTrait;
    protected static array $properties = [
        ["string", "name"]
    ];
    
    public static array $plurals = ["Users"];
}