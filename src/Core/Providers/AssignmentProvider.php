<?php

namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\GroupCategory;
use CanvasApiLibrary\Core\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderInterface;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;


class AssignmentProvider extends AbstractProvider implements AssignmentProviderInterface{
    use AssignmentProviderProperties;

    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($canvasCommunicator,
        new ModelPopulationConfigBuilder(Assignment::class)
            ->from("group_category_id")->to("group_category")->asModel(GroupCategory::class)
            ->from("course_id")->to("course")->asModel(Course::class));
    }

    /**
     * @return SuccessResult<Assignment>|UnauthorizedResult|NotFoundResult|ErrorResult
     */
    public function populateAssignment(Assignment $assignment): SuccessResult|UnauthorizedResult|NotFoundResult|ErrorResult{
        return $this->Get(
            "/courses/{$assignment->course->id}/assignments/$assignment->id",
            $assignment->getContext(),
            $this->modelPopulator->withInstance($assignment)
        );
    }
}