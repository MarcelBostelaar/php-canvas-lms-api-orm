<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\AssignmentStubProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\CourseBoundIdTrait;

abstract class AssignmentStub extends AbstractCanvasPopulatedModel{
    use CourseBoundIdTrait;
    use AssignmentStubProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["AssignmentStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
