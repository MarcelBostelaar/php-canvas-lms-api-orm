<?php
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\Course;

interface SectionProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
    /**
    * @param Section[] $sections
    * @return Section[]
    */
    public function populateSections(array $sections) : array;

    /**
    * @param Course[] $courses
    * @return Lookup<Course, Section>
    */
    public function getAllSectionsInCourses(array $courses) : Lookup;

    /**
    * @param Course $course
    * @return mixed
    */
    public function getAllSectionsInCourse(Course $course) : mixed;

    /**
    * @param Section $section
    * @return Section
    */
    public function populateSection(Section $section) : Section;

}
