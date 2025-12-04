<?php
namespace CanvasApiLibrary\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Models\Section;
use CanvasApiLibrary\Models\Course;

interface SectionProviderInterface extends HandleEmittedInterface{

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
