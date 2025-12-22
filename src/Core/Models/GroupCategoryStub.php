<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\GroupCategoryStubProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdOptionalUserCourseContextTrait;

abstract class GroupCategoryStub extends AbstractCanvasPopulatedModel{
    use DomainBoundIdOptionalUserCourseContextTrait;
    use GroupCategoryStubProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["GroupCategoryStubs"];
}
