<?php

namespace CanvasApiLibrary\Core\Models;

use CanvasApiLibrary\Core\Models\IdentityTraits\CourseBoundIdTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Generated\AssignmentProperties;

class Assignment extends AbstractCanvasPopulatedModel{
    use AssignmentProperties;
    use CourseBoundIdTrait;
    protected static array $properties = [
        [GroupCategory::class, "group_category"]
    ];

    protected static array $plurals = ["Assignments"];
}