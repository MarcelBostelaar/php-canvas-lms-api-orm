<?php

namespace CanvasApiLibrary\Providers\Utility\ModelPopulator;

use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

/**
 * Consumer interface responsible for applying provided data to a model instance.
 * Implementations define how the data is consumed and whether null data is acceptable.
 */
interface ConsumerInterface
{
    /**
     * Consumes provided data and applies it to the given model instance.
     *
     * @param mixed $data Arbitrary data to be consumed
     * @param AbstractCanvasPopulatedModel $model A model instance to mutate/populate
     * @return string[] List of error messages encountered during consumption (empty when none)
     */
    public function consumeData(mixed $data, AbstractCanvasPopulatedModel $model): array;

    /**
     * Whether this consumer accepts null data inputs without error.
     */
    public function getAcceptsNull(): bool;

    /**
     * Sets whether this consumer accepts null data inputs without error.
     */
    public function setAcceptsNull(bool $acceptsNull): void;
}
