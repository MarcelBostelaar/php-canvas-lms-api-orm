<?php

namespace CanvasApiLibrary\Models;

use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\AssignmentProperties;
use CanvasApiLibrary\Models\ContextPopulationTraits\CourseIdentityTrait;

class Assignment extends AbstractCanvasPopulatedModel{
    use AssignmentProperties;
    use CourseIdentityTrait;
    protected static $properties = [
        [GroupCategory::class, "group_category"],
        [Course::class, "course"]
    ];

    public static array $plurals = ["Assignments"];
}