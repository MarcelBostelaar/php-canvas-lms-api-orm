<?php

namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\AssignmentStub;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\GroupCategory;
use CanvasApiLibrary\Core\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\AssignmentWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;

/**
 * @implements AssignmentProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<Assignment>
 */
class AssignmentProvider extends AbstractProvider implements AssignmentProviderInterface{
    use AssignmentProviderProperties;
    use AssignmentWrapperTrait;

    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($canvasCommunicator,
        new ModelPopulationConfigBuilder(Assignment::class)
            ->from("group_category_id")->to("group_category")->asModel(GroupCategory::class)
            ->from("course_id")->to("course")->asModel(Course::class));
    }

    /**
     * @param AssignmentStub $assignment
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<Assignment>|UnauthorizedResult
     */
    public function populateAssignment(AssignmentStub $assignment, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->Get(
            "/courses/{$assignment->course->id}/assignments/$assignment->id",
            $assignment->getContext()
        );
    }
}