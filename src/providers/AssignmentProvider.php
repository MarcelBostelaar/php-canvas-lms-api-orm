<?php

namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\GroupCategory;
use CanvasApiLibrary\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Providers\Interfaces\AssignmentProviderInterface;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Services\CanvasCommunicator;
use CanvasApiLibrary\Services\StatusHandlerInterface;


class AssignmentProvider extends AbstractProvider implements AssignmentProviderInterface{
    use AssignmentProviderProperties;

    public function __construct(
        StatusHandlerInterface $statusHandler,
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($statusHandler, $canvasCommunicator,
        new ModelPopulationConfigBuilder(Assignment::class)
            ->from("group_category_id")->to("group_category")->asModel(GroupCategory::class)
            ->from("course_id")->to("course")->asModel(Course::class));
    }

    public function populateAssignment(Assignment $assignment) : Assignment{
        echo $assignment->course->id;
        $this->Get(
        "/courses/{$assignment->course->id}/assignments/$assignment->id",
        $assignment->getContext(),
        $this->modelPopulator->withInstance($assignment));
        return $assignment;
    }
}