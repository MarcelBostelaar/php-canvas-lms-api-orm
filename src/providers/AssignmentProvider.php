<?php

namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\GroupCategory;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;


class AssignmentProvider extends AbstractProvider{
    use AssignmentProviderProperties;

    public function populateAssignment(Assignment $assignment) : Assignment{
        // $this->populateModel($assignment);
    }

    /**
     * Summary of populateModel
     * @param Domain $domain
     * @param Assignment $model
     * @param mixed $data
     * @return Assignment
     */
    protected function populateModel(Domain $domain, $model, mixed $data) : Assignment{
        //Replace array_map_to_models with a builder pattern. Save builder pattern statically in the provider.
        //Use the builder pattern to populate the model easily.
        //Builder pattern will not save the domain, but is passed it on build.
        //Builder pattern returns new model class.
    }
}