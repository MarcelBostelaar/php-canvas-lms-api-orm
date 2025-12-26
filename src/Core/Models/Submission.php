<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\SubmissionProperties;

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
    protected function getClassName(): string{
        return $this::class;
    }
}