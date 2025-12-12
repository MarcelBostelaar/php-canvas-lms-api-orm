<?php

namespace CanvasApiLibrary\Core\Providers\Utility\ModelPopulator;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Utility\ModelInterface;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;
use LogicException;

class ConsumerEmitter implements ConsumerInterface{

    public function __construct(readonly private HandleEmittedInterface $emitHandler){}
    
    /**
     * Consumes provided data and applies it to the given model instance.
     *
     * @param mixed $data Arbitrary data to be consumed
     * @param AbstractCanvasPopulatedModel $model A model instance to mutate/populate
     * @param array<int, ModelInterface> $context A list of context items.
     * @return string[] List of error messages encountered during consumption (empty when none)
     */
    public function consumeData(mixed $data, AbstractCanvasPopulatedModel $model, array $context): array {
        if($data === null){
            return ["Cannot pass null data to emittion handler"];
        }
        $this->emitHandler->HandleEmitted($data, $context);
        return [];
    }

    /**
     * Whether this consumer accepts null data inputs without error.
     */
    public function getAcceptsNull(): bool{
        return false;
    }

    /**
     * Sets whether this consumer accepts null data inputs without error.
     */
    public function setAcceptsNull(bool $acceptsNull): void{
        throw new LogicException("Cannot make emitter accept null");
    }
}