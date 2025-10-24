<?php

namespace CanvasApiLibrary\Models;
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
    protected static array $properties = [
        [User::class, "user"],
        [Assignment::class, "assignment"],
        [Course::class, "course"]
    ];
    protected static array $nullableProperties = [
        ["string", "url"], 
        [\DateTime::class, "submitted_at"],
        [Section::class, "section"]
    ];
    
    public static array $plurals = ["Submissions"];

    public function validateSkeleton(): bool{
        return (isset($this->course_id) || isset($this->section_id)) 
            && isset($this->assignment_id) && isset($this->id);
    }
}