<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\CourseProperties;

final class Course extends AbstractCanvasPopulatedModel{
    use CourseProperties;
    public static array $plurals = ["Courses"];
}