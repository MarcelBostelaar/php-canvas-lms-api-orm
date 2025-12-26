<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\GroupStubProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdOptionalUserCourseContextTrait;

abstract class GroupStub extends AbstractCanvasPopulatedModel{
    use DomainBoundIdOptionalUserCourseContextTrait;
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
