<?php

namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\GroupCategory;
use CanvasApiLibrary\Core\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Core\Providers\Generated\Traits\CourseProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\CourseProviderInterface;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;


class CourseProvider extends AbstractProvider implements CourseProviderInterface{
    use CourseProviderProperties;

    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct( $canvasCommunicator,
        new ModelPopulationConfigBuilder(Course::class));
    }

    /**
     * @param Domain $domain
     * @return ErrorResult|NotFoundResult|SuccessResult<Course[]>|UnauthorizedResult
     */
    public function getAllCoursesInDomain(Domain $domain): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->GetMany("/courses", $domain->getContext());
    }
}