<?php

namespace CanvasApiLibrary\Models;

use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

final class Assignment extends AbstractCanvasPopulatedModel{
    protected static $properties = [
        [GroupCategory::class, "group"]
    ];

    public static array $plurals = ["Assignments"];
}