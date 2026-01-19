<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\OutcomeProperties;

final class Outcome extends OutcomeStub{
    use OutcomeProperties;
    public static array $plurals = ["Outcomes"];

    protected static array $properties = [
        ["string", "title"],
        ["string", "description"],
        ["int", "points_possible"],
        ["int", "mastery_points"],
        ["string", "calculation_method"]
    ];
    protected static array $nullableProperties = [
        ["int", "calculation_int"]
    ];

    protected function getClassName(): string{
        return $this::class;
    }
}