<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Providers\Generated\Traits;

use CanvasApiLibrary;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Models\Section;
use CanvasApiLibrary\Models\Course;

trait SectionProviderProperties{
    abstract public function populateSection(Section $section);
    
    /**
     * Array variant of populateSection
     * @param Section[] $sections
     * @return Section[]
     */
    public function populateSections(array $sections): array{
        return array_map(fn($x) => $this->populateSection($x), $sections);
    }

    abstract public function getAllSectionsInCourse(Course $course) : array;
    
    /**
     * Summary of getAllSectionsInCourses
     * @param Course[] $courses
     * @return Lookup<Course, Section>
     */
    public function getAllSectionsInCourses(array $courses): Lookup{
        $lookup = new Lookup();
        foreach($courses as $course){
            $lookup->add($course, $this->getAllSectionsInCourse($course));
        }
        return $lookup;
    }
}
