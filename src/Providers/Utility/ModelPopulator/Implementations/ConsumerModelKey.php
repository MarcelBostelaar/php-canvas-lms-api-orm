<?php

namespace CanvasApiLibrary\Providers\Utility\ModelPopulator;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Utility\ModelInterface;

class ConsumerModelKey implements ConsumerInterface{

    public function __construct(readonly string $key){}
    /**
     * Consumes provided data and applies it to the given model instance.
     *
     * @param mixed $data Arbitrary data to be consumed
     * @param AbstractCanvasPopulatedModel $model A model instance to mutate/populate
     * @param ModelInterface[] $context A list of context items.
     * @return string[] List of error messages encountered during consumption (empty when none)
     */
    public function consumeData(mixed $data, AbstractCanvasPopulatedModel $model, array ...$context): array{
        if($data === null){
            if(!$this->acceptsNull){
                return [
                    "Cannot set null value in field $this->key of model"
                ];
            }
        }
        $model->{$this->key} = $data;
        return [];
    }

    private $acceptsNull = false;
    /**
     * Whether this consumer accepts null data inputs without error.
     */
    public function getAcceptsNull(): bool{
        return $this->acceptsNull;
    }

    /**
     * Sets whether this consumer accepts null data inputs without error.
     */
    public function setAcceptsNull(bool $acceptsNull): void{
        $this->acceptsNull = $acceptsNull;
    }
}
