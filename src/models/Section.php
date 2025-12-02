<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\IdentityTraits\CourseBoundIdTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\SectionProperties;

final class Section extends AbstractCanvasPopulatedModel{
    use SectionProperties;
    use CourseBoundIdTrait;
    protected static array $properties = [
        ["string", "name"]
    ];

    
    public static array $plurals = ["Sections"];
}