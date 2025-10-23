<?php

namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\GroupCategory;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;


class AssignmentProvider extends AbstractProvider{
    use AssignmentProviderProperties;

    protected static ModelPopulationConfigBuilder $modelPopulator = 
        new ModelPopulationConfigBuilder(Assignment::class)
        ->from("group_category_id")->to("group_category")->asModel(GroupCategory::class)
        ->from("course_id")->to("course")->asModel(Course::class);

    public function populateAssignment(Assignment $assignment) : Assignment{
        $this->Get($assignment->getDomain(), 
        "/api/v1/courses/{$assignment->course->id}/assignments/$assignment->id",
        self::$modelPopulator->withInstance($assignment));
        return $assignment;
    }
}