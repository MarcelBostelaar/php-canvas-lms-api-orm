<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\GroupStubProperties;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdOptionalCourseContextTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdOptionalUserCourseContextTrait;

abstract class GroupStub extends AbstractCanvasPopulatedModel{
    use DomainBoundIdOptionalCourseContextTrait;
    use GroupStubProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["GroupStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
