<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\UserAssignmentBoundIdTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Generated\SubmissionCommentProperties;

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