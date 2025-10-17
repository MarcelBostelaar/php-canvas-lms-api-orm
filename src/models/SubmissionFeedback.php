<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\SubmissionFeedbackProperties;

final class SubmissionFeedback extends AbstractCanvasPopulatedModel{
    use SubmissionFeedbackProperties;
    protected static array $properties = [
        ["string", "feedbackGiver"],
        ["string", "comment"],
        [\DateTime::class, "date"]
    ];

    public static array $plurals = ["SubmissionFeedbacks"];
}

$x = new SubmissionFeedback(new Domain("test.nl"), 1);
$x->date;