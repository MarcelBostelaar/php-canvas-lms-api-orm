<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\SubmissionCommentProperties;

class SubmissionComment extends SubmissionCommentStub{
    use SubmissionCommentProperties;
    protected static array $properties = [
        ["string", "feedback_giver"],
        ["string", "comment"],
        [\DateTime::class, "date"],
    ];

    public static array $plurals = ["SubmissionComments"];
}