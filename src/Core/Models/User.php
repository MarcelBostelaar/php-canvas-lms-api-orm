<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdOptionalCourseContextTrait;
use CanvasApiLibrary\Core\Models\Generated\UserProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;

class User extends AbstractCanvasPopulatedModel{
    use UserProperties;
    use DomainBoundIdOptionalCourseContextTrait;
    protected static array $properties = [
        ["string", "name"]
    ];
    
    public static array $plurals = ["Users"];
}