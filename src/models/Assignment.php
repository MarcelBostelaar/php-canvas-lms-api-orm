<?php

namespace CanvasApiLibrary\Models;

use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

final class Assignment extends AbstractCanvasPopulatedModel{
    protected static $properties = [
        [GroupCategory::class, "group"]
    ];

    public static function getPluralNames(): array{
        return ["Assignments"];
    }
}