<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Models\Submission;
use CanvasApiLibrary\Models\Domain;

trait SubmissionProviderProperties{
    abstract public function populateSubmission(Submission $submission);
    
    /**
     * Array variant of populateSubmission
     * @param Submission[] $submissions
     * @return Submission[]
     */
    public function populateSubmissions(array $submissions): array{
        return array_map(fn($x) => $this->populateSubmission($x), $submissions);
    }
}
