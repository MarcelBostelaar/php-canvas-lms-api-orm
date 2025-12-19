<?php
namespace CanvasApiLibrary\Core\Providers;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SectionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SectionProviderInterface;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;


/**
 * Provider for Canvas API section operations
 * 
 * @method Lookup<Models\Course, Models\Section> getAllSectionsInCourses() Virtual method to get all sections in a course
 */
class SectionProvider extends AbstractProvider implements SectionProviderInterface{
    use SectionProviderProperties;

    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($canvasCommunicator,
        new ModelPopulationConfigBuilder(Section::class)
                ->keyCopy("name"));
    }

    /**
     * @param \CanvasApiLibrary\Core\Models\Course $course
     * @return Models\Section[]
     */
    public function getAllSectionsInCourse(Course $course) : array{
        return $this->GetMany("/courses/$course->id/sections", $course->getContext());
    }

    /**
     * @param Models\Section $section
     * @return Models\Section
     */
    public function populateSection(Section $section): Section{
        $courseID = $section->course->id;
        $this->Get("/courses/$courseID/sections/$section->id", 
        $section->getContext(), 
        $this->modelPopulator->withInstance($section));
        return $section;
    }
}