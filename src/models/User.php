<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Generated\UserProperties;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

class User extends AbstractCanvasPopulatedModel{
    use UserProperties;
    protected static array $properties = [
        ["string", "name"]
    ];
    
    public static array $plurals = ["Users"];
}