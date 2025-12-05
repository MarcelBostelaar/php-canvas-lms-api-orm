<?php

namespace CanvasApiLibrary\Core\Providers\Utility\ModelPopulator;
use Closure;
use CanvasApiLibrary\Core\Models\Domain;

class ProcessorClosure implements ProcessorInterface{
    public function __construct(readonly private Closure $processFunc){}

    public function process(mixed $data, $context): array{
        return [
            "data" => ($this->processFunc)($data),
            "errors" => [],
            "continue" => true
        ];
    }
}
