<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalCourseContextTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalUserContextTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdTrait;
use CanvasApiLibrary\Core\Models\Generated\UserProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;

class User extends AbstractCanvasPopulatedModel{
    use UserProperties;
    use DomainBoundIdTrait;
    use OptionalCourseContextTrait;
    protected static array $properties = [
        ["string", "name"]
    ];
    
    public static array $plurals = ["Users"];
}