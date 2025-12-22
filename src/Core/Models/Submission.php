<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\SubmissionProperties;

/**
 * @property User $user
 * @property Assignment $assignment
 * @property ?string $url
 * @property ?\DateTime $submittedAt
 */
final class Submission extends SubmissionStub{
    use SubmissionProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
        ["string", "url"], 
        [\DateTime::class, "submitted_at"],
        [SectionStub::class, "section"]
    ];
    
    public static array $plurals = ["Submissions"];
}