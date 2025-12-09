<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Core\Providers\Generated\Traits;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\Section;

trait SectionProviderProperties{
    
    
    
    abstract public function populateSection(Section$section);
    
    /**
    * Plural version of populateSection
    * @param Section[] $sections
    * @return Section[]

    */
    public function populateSections(array $sections) : array{
        return array_map(fn($x) => $this->populateSection($x), $sections);
    }
    
    
    abstract public function getAllSectionsInCourse(Course $course) : array;
    
    /**
     * Summary of getAllSectionsInCourses
     * @param Course[] $courses
     * @return Lookup<Course, Course>
     */
    public function getAllSectionsInCourses(array $courses): Lookup{
        $lookup = new Lookup();
        foreach($courses as $course){
            $lookup->add($course, $this->getAllSectionsInCourse($course));
        }
        return $lookup;
    }
}
