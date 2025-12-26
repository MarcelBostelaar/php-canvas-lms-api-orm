<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\SubmissionStubProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\UserAssignmentBoundIdTrait;

abstract class SubmissionStub extends AbstractCanvasPopulatedModel{
    use UserAssignmentBoundIdTrait;
    use SubmissionStubProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["SubmissionStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
