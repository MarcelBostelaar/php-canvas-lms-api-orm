<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Section;
use CanvasApiLibrary\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;


/**
 * Provider for Canvas API section operations
 * 
 * @method Lookup<Models\Course, Models\Section> getAllSectionsInCourses() Virtual method to get all sections in a course
 */
class SectionProvider extends AbstractProvider{
    use SectionProviderProperties;

    protected static $modelPopulator = 
    new ModelPopulationConfigBuilder(Section::class)
    ->keyCopy("name");

    /**
     * @param \CanvasApiLibrary\Models\Course $course
     * @return Models\Section[]
     */
    public function getAllSectionsInCourse(Course $course) : array{
        return $this->GetMany("/courses/$course->id/sections", $course->getContext());
    }

    public function populateSection(Section $section): Section{
        $courseID = $section->course->id;
        $this->Get("/courses/$courseID/sections/$section->id", $section->getContext(), self::$modelPopulator->withInstance($section));
        return $section;
    }
}