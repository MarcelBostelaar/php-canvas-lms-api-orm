<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\CourseStubProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdTrait;

abstract class CourseStub extends AbstractCanvasPopulatedModel{
    use DomainBoundIdTrait;
    use CourseStubProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["CourseStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
