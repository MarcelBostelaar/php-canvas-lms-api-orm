<?php

namespace CanvasApiLibrary\Core\Models;

use CanvasApiLibrary\Core\Models\Generated\AssignmentProperties;

class Assignment extends AssignmentStub{
    use AssignmentProperties;
    protected static array $properties = [
        [GroupCategoryStub::class, "group_category"]
    ];

    protected static array $plurals = ["Assignments"];
}