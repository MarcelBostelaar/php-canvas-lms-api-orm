<?php

namespace CanvasApiLibrary\Core\Providers\Utility\Results;

/**
 * @template TValue
 */
class SuccessResult
{
    use ResultMonadTrait;
    /**
     * @param TValue $value
     */
    public function __construct(public readonly mixed $value)
    {
    }
}
