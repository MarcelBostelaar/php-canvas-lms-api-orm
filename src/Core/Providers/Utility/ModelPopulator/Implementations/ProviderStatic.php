<?php

namespace CanvasApiLibrary\Core\Providers\Utility\ModelPopulator;

class ProviderStatic implements ProviderInterface{

    public function __construct(readonly mixed $data){}


    /**
     * Retrieves data and any errors encountered while producing it.
     *
     * @return array{data:mixed, errors:string[]} An associative array where:
     *  - data: The produced data
     *  - errors: A list of error messages (empty when none)
     */    
    public function getData(array $source): array{
        return [
            "data" => $this->data,
            "errors" => []
        ];
    }

    public function getDescription(): string {
        return "Static data provided: " . serialize($this->data);
    }
}