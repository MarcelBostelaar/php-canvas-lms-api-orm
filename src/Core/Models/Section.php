<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\CourseBoundIdTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Generated\SectionProperties;

final class Section extends AbstractCanvasPopulatedModel{
    use SectionProperties;
    use CourseBoundIdTrait;
    protected static array $properties = [
        ["string", "name"]
    ];

    
    public static array $plurals = ["Sections"];
}