<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\SectionStubProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\CourseBoundIdTrait;

abstract class SectionStub extends AbstractCanvasPopulatedModel{
    use CourseBoundIdTrait;
    use SectionStubProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["SectionStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
