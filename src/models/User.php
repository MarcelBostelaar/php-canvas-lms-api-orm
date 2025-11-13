<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\ContextPopulationTraits\DomainIdentityTrait;
use CanvasApiLibrary\Models\Generated\UserProperties;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

class User extends AbstractCanvasPopulatedModel{
    use UserProperties;
    use DomainIdentityTrait;
    protected static array $properties = [
        ["string", "name"]
    ];
    
    public static array $plurals = ["Users"];
}