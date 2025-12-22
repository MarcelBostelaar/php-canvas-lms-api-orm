<?php
namespace CanvasApiLibrary\Core\Providers;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\SectionStub;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SectionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SectionProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\SectionWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * @implements SectionProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<Section>
 */
class SectionProvider extends AbstractProvider implements SectionProviderInterface{
    use SectionProviderProperties;
    use SectionWrapperTrait;

    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($canvasCommunicator,
        new ModelPopulationConfigBuilder(Section::class)
                ->keyCopy("name"));
    }

    /**
     * @param \CanvasApiLibrary\Core\Models\CourseStub $course
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<Section[]>|UnauthorizedResult
     */
    public function getAllSectionsInCourse(CourseStub $course, bool $skipCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->GetMany("/courses/$course->id/sections", $course->getContext());
    }

    /**
     * @param Models\SectionStub $section
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<Section>|UnauthorizedResult
     */
    public function populateSection(SectionStub $section, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        $courseID = $section->course->id;
        return $this->Get("/courses/$courseID/sections/$section->id", 
        $section->getContext()
        );
    }
}