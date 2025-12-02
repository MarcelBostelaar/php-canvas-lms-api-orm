<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\IdentityTraits\UserAssignmentBoundIdTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\SubmissionProperties;

/**
 * @property User $user
 * @property Assignment $assignment
 * @property ?string $url
 * @property ?\DateTime $submittedAt
 */
final class Submission extends AbstractCanvasPopulatedModel{
    use SubmissionProperties;
    use UserAssignmentBoundIdTrait;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
        ["string", "url"], 
        [\DateTime::class, "submitted_at"],
        [Section::class, "section"]
    ];
    
    public static array $plurals = ["Submissions"];
}