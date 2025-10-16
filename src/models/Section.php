<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

final class Section extends AbstractCanvasPopulatedModel{
    protected static array $properties = [
        ["string", "name"],
        [Course::class, "course"]
    ];

    public static function getPluralNames(): array{
        return ["Sections"];
    }
}