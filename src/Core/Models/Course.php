<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\CourseProperties;

final class Course extends CourseStub{
    use CourseProperties;
    public static array $plurals = ["Courses"];
    protected function getClassName(): string{
        return $this::class;
    }
}