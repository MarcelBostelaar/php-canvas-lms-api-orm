<?php

namespace CanvasApiLibrary\Core\Providers\Utility\ModelPopulator;
use Closure;
use CanvasApiLibrary\Core\Models\Utility\ModelInterface;

class ProcessorClosure implements ProcessorInterface{
    public function __construct(readonly private Closure $processFunc){}

    /**
     * @param array<int, ModelInterface> $context
     */
    public function process(mixed $data, array $context): array {
        return [
            "data" => ($this->processFunc)($data),
            "errors" => [],
            "continue" => true
        ];
    }
}
