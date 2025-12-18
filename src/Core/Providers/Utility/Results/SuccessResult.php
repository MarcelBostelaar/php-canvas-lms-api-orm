<?php

namespace CanvasApiLibrary\Core\Providers\Utility\Results;

/**
 * @template TValue
 */
class SuccessResult
{
    /**
     * @param TValue $value
     */
    public function __construct(public readonly mixed $value)
    {
    }
}
