<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

/**
 * @property string $feedbackGiver
 * @property string $comment
 * @property \DateTime $date
 */
final class SubmissionFeedback extends AbstractCanvasPopulatedModel{
    protected static array $properties = [
        ["string", "feedbackGiver"],
        ["string", "comment"],
        [\DateTime::class, "date"]
    ];

    public static array $plurals = ["SubmissionFeedbacks"];
}