<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Generated\CourseProperties;

final class Course extends AbstractCanvasPopulatedModel{
    use CourseProperties;
    use DomainBoundIdTrait;
    public static array $plurals = ["Courses"];
}