<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\ContextPopulationTraits\DomainIdentityTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\CourseProperties;

final class Course extends AbstractCanvasPopulatedModel{
    use CourseProperties;
    use DomainIdentityTrait;
    public static array $plurals = ["Courses"];
}