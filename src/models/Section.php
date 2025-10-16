<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use Src\Models\Generated\SectionProperties;

final class Section extends AbstractCanvasPopulatedModel{
    use SectionProperties;
    protected static array $properties = [
        ["string", "name"],
        [Course::class, "course"]
    ];

    
    public static array $plurals = ["Sections"];
}