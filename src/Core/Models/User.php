<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\UserProperties;;

class User extends UserStub{
    use UserProperties;
    protected static array $properties = [
        ["string", "name"]
    ];
    
    public static array $plurals = ["Users"];
    protected function getClassName(): string{
        return $this::class;
    }
}