<?php

namespace CanvasApiLibrary\Models;

use CanvasApiLibrary\Models\IdentityTraits\CourseBoundIdTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\AssignmentProperties;

class Assignment extends AbstractCanvasPopulatedModel{
    use AssignmentProperties;
    use CourseBoundIdTrait;
    protected static array $properties = [
        [GroupCategory::class, "group_category"]
    ];

    public static array $plurals = ["Assignments"];
}