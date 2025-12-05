<?php

namespace CanvasApiLibrary\Core\Providers\Utility;
use CanvasApiLibrary\Core\Models\Utility\ModelInterface;

interface HandleEmittedInterface{
    /**
     * Handles emitted data for performance gain.
     * @param mixed $data The full dictionary data to be consumed
     * @param ModelInterface[] $context A list of context data.
     * @return void
     */
    public function HandleEmitted(mixed $data, array $context);
}