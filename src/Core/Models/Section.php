<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\SectionProperties;

final class Section extends SectionStub{
    use SectionProperties;
    protected static array $properties = [
        ["string", "name"]
    ];

    
    public static array $plurals = ["Sections"];
    protected function getClassName(): string{
        return $this::class;
    }
}