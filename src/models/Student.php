<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\StudentProperties;

final class Student extends AbstractCanvasPopulatedModel{
    use StudentProperties;
    protected static array $properties = [
        ["string", "name"]
    ];
    
    public static array $plurals = ["Students"];
}