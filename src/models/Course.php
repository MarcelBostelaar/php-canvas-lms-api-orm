<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\IdentityTraits\DomainBoundIdTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\CourseProperties;

final class Course extends AbstractCanvasPopulatedModel{
    use CourseProperties;
    use DomainBoundIdTrait;
    public static array $plurals = ["Courses"];
}