<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\UserStubProperties;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdOptionalCourseContextTrait;
use CanvasApiLibrary\Core\Models\Generated\UserProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;

abstract class UserStub extends AbstractCanvasPopulatedModel{
    use UserStubProperties;
    use DomainBoundIdOptionalCourseContextTrait;
    protected static array $properties = [
    ];
    
    public static array $plurals = ["UserStubs"];
}