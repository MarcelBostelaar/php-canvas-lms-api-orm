<?php

namespace CanvasApiLibrary\Providers\Utility\ModelPopulator;
use CanvasApiLibrary\Models\Domain;

class ProcessorModel implements ProcessorInterface{
    public function __construct(readonly private string $modelclass){}

    public function process(mixed $data, array $context): array{
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
        $newModel = new $this->modelclass();
        $newModel->id = $data;
        $newModel->populateWithContext($context);
        if(!$newModel->validateSkeleton()){
            return [
                "data" => null,
                "errors" => ["Not all needed fields for the model $newModel have been set."],
                "continue" => false
            ];
        }
        return [
            "data" => $newModel,
            "errors" => [],
            "continue" => true
        ];
    }
}
