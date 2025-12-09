<?php

namespace CanvasApiLibrary\Core\Models;

use CanvasApiLibrary\Core\Models\Generated\UserDisplayProperties;

abstract class UserDisplay extends UserStub{
    use UserDisplayProperties;
    protected static array $properties = [
        ["string", "short_name"],
        ["string", "avatar_image_url"],
        ["string", "html_url"],
    ];
    
    public static array $plurals = ["UserDisplays"];
}