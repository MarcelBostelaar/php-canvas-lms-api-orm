<?php

namespace CanvasApiLibrary\Core\Providers\Utility\Results;

class ErrorResult
{
    /**
     * @param string[] $errors
     */
    public function __construct(public readonly array $errors = [])
    {
    }
}
