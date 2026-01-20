<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\SubmissionCommentStubProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\UserAssignmentBoundIdTrait;

class SubmissionCommentStub extends AbstractCanvasPopulatedModel{
    use UserAssignmentBoundIdTrait;
    use SubmissionCommentStubProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["SubmissionCommentStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
