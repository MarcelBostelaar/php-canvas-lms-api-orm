<?php

namespace CanvasApiLibrary\Models;

use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\AssignmentProperties;

class Assignment extends AbstractCanvasPopulatedModel{
    use AssignmentProperties;
    protected static $properties = [
        [GroupCategory::class, "group_category"],
        [Course::class, "course"]
    ];

    public static array $plurals = ["Assignments"];

    public function validateSkeleton(): bool{
        return isset($this->course_id) && isset($this->id);
    }
}