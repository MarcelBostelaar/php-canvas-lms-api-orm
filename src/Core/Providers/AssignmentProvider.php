<?php

namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\GroupCategory;
use CanvasApiLibrary\Core\Providers\Generated\Traits\AssignmentProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderInterface;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Services\StatusHandlerInterface;


class AssignmentProvider extends AbstractProvider implements AssignmentProviderInterface{
    use AssignmentProviderProperties;

    public function __construct(
        StatusHandlerInterface $statusHandler,
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($statusHandler, $canvasCommunicator,
        new ModelPopulationConfigBuilder(Assignment::class)
            ->from("group_category_id")->to("group_category")->asModel(GroupCategory::class)//TODO add optional context awareness to builder. Special asModel method, of een addon die de consumer wrapt. Maar vgm moet het eigenlijk in de model zelf omdat als je een model uit een andere model haalt moet de context meegegeven worden.
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