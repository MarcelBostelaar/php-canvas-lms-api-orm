<?php

namespace CanvasApiLibrary\Core\Providers\Utility\ModelPopulator;

/**
 * Provider interface responsible for producing arbitrary data for population workflows.
 * Implementations should encapsulate how data is fetched or assembled.
 */
interface ProviderInterface
{
    /**
     * Retrieves data and any errors encountered while producing it.
     *
     * @return array{data:mixed, errors:string[]} An associative array where:
     *  - data: The produced arbitrary data
     *  - errors: A list of error messages (empty when none)
     */
    public function getData(array $source): array;

    public function getDescription(): string;
}
