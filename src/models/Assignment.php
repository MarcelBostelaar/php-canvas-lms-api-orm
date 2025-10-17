<?php

namespace CanvasApiLibrary\Models;

use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\AssignmentProperties;

final class Assignment extends AbstractCanvasPopulatedModel{
    use AssignmentProperties;
    protected static $properties = [
        [GroupCategory::class, "group"]
    ];

    public static array $plurals = ["Assignments"];
}