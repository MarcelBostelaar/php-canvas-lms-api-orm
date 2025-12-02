<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\IdentityTraits\UserAssignmentBoundIdTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\SubmissionCommentProperties;

class SubmissionComment extends AbstractCanvasPopulatedModel{
    use SubmissionCommentProperties;
    use UserAssignmentBoundIdTrait;
    protected static array $properties = [
        ["string", "feedback_giver"],
        ["string", "comment"],
        [\DateTime::class, "date"],
    ];

    public static array $plurals = ["SubmissionComments"];
}