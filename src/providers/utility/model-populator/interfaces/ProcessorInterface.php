<?php

namespace CanvasApiLibrary\Providers\Utility\ModelPopulator;
use CanvasApiLibrary\Models\Domain;


/**
 * Processor interface used to transform data before consumption.
 */
interface ProcessorInterface
{
    /**
     * Consumes input data and returns transformed data along with errors and a continue flag.
     *
     * @param mixed $data Input data to process
     * @return array{data:mixed, errors:string[], continue:bool} An associative array with:
     *  - data: Transformed/new data
     *  - errors: List of error messages (empty when none)
     *  - continue: Whether the pipeline should continue processing/consumption
     */
    public function process(mixed $data, Domain $domain): array;
}
