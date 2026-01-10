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
use CanvasApiLibrary\Core\Providers\Traits\CourseWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\ClientIDProvider;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * @implements CourseProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<Course>
 */
class CourseProvider extends AbstractProvider implements CourseProviderInterface{
    use CourseProviderProperties;
    use CourseWrapperTrait;

    public function __construct(
        CanvasCommunicator $canvasCommunicator,
        ClientIDProvider $clientIDProvider
    ) {
        parent::__construct( $canvasCommunicator,
        new ModelPopulationConfigBuilder(Course::class),
        $clientIDProvider
        );
    }

    /**
     * @param Domain $domain
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @param bool $doNotCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<Course[]>|UnauthorizedResult
     */
    public function getAllCoursesInDomain(Domain $domain, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->GetMany("/courses", $domain->getContext());
    }
}