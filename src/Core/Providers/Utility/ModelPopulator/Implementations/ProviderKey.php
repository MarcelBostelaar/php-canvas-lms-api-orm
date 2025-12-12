<?php

namespace CanvasApiLibrary\Core\Providers\Utility\ModelPopulator;

class ProviderKey implements ProviderInterface{

    public function __construct(readonly string $key){}


    /**
     * Retrieves data and any errors encountered while producing it.
     *
     * @return array{data:mixed, errors:string[]} An associative array where:
     *  - data: The produced data
     *  - errors: A list of error messages (empty when none)
     */    
    public function getData(array $source): array{
        if(!isset($source[$this->key])){
            return [
                "data"=> null,
                "errors" => ["Could not find $this->key in source data"]
            ];
        }
        return [
            "data" => $source[$this->key],
            "errors" => []
        ];
    }

    public function getDescription(): string {
        return "Key: $this->key";
    }
}