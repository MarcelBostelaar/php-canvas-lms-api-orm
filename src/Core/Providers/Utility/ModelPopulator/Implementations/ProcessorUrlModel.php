<?php

namespace CanvasApiLibrary\Core\Providers\Utility\ModelPopulator;
use CanvasApiLibrary\Core\Models\Utility\ModelInterface;

class ProcessorUrlModel implements ProcessorInterface{
    /**
     * @var class-string
     */
    public function __construct(readonly private string $modelclass){}

    /**
     * @param array<int, ModelInterface> $context
     */
    public function process(mixed $data, array $context): array {
        if($data === null){
            return [
                "data" => null,
                "errors" => [],
                "continue" => true
            ];
        }
        if(!is_array($data) || !isset($data["url"]) || !isset($data["id"])){
            return [
                "data" => null,
                "errors" => ["Cannot turn $data into a model id with url, id and url must be present."],
                "continue" => false
            ];
        }
        $newModel = new $this->modelclass();
        $newModel->id = $data["id"];
        $newModel->url = $data["url"];
        $newModel->populateWithContext($context);
        if(!$newModel->validateIdentityIntegrity()){
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
