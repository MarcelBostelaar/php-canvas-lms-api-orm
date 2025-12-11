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
use CanvasApiLibrary\Core\Services\StatusHandlerInterface;


class CourseProvider extends AbstractProvider implements CourseProviderInterface{
    use CourseProviderProperties;

    public function __construct(
        StatusHandlerInterface $statusHandler,
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($statusHandler, $canvasCommunicator,
        new ModelPopulationConfigBuilder(Course::class));
    }

    /**
     * @param Domain $domain
     * @return Course[]
     */
    public function getAllCoursesInDomain(Domain $domain): array{
        return $this->GetMany("/courses", $domain->getContext());
    }
}