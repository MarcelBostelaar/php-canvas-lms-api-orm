<?php

namespace CanvasApiLibrary\Providers\Utility\ModelPopulator;
use Closure;
use CanvasApiLibrary\Models\Domain;

class ProcessorModel implements ProcessorInterface{
    public function __construct(readonly private string $modelclass){}

    public function process(mixed $data, Domain $domain): array{
        if($data === null){
            return [
                "data" => null,
                "errors" => [],
                "continue" => true
            ];
        }
        if(!is_int($data)){
            return [
                "data" => null,
                "errors" => ["Cannot turn $data into a model id, must be an int."],
                "continue" => false
            ];
        }
        $newModel = new $this->modelclass($domain);
        $newModel->id = $data;
        return [
            "data" => $newModel,
            "errors" => [],
            "continue" => true
        ];
    }
}
