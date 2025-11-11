<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\SubmissionCommentProperties;

class SubmissionComment extends AbstractCanvasPopulatedModel{
    use SubmissionCommentProperties;
    protected static array $properties = [
        ["string", "feedback_giver"],
        ["string", "comment"],
        [\DateTime::class, "date"],
        [Course::class, "course"],
        [Assignment::class, "assignment"],
        [User::class, "user"],
    ];

    public static array $plurals = ["SubmissionComments"];
}