<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\SubmissionProperties;

/**
 * @property Student $student
 * @property Assignment $assignment
 * @property ?string $url
 * @property ?\DateTime $submittedAt
 */
final class Submission extends AbstractCanvasPopulatedModel{
    use SubmissionProperties;
    protected static array $properties = [
        [Student::class, "student"],
        [Assignment::class, "assignment"]
    ];
    protected static array $nullableProperties = [
        ["string", "url"], 
        [\DateTime::class, "submittedAt"]
    ];
    
    public static array $plurals = ["Submissions"];
}