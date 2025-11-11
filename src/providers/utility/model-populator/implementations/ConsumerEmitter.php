<?php

namespace CanvasApiLibrary\Providers\Utility\ModelPopulator;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Providers\Utility\HandleEmittedInterface;
use LogicException;

class ConsumerEmitter implements ConsumerInterface{

    public function __construct(readonly private HandleEmittedInterface $emitHandler){}
    
    /**
     * Consumes provided data and applies it to the given model instance.
     *
     * @param mixed $data Arbitrary data to be consumed
     * @param AbstractCanvasPopulatedModel $model A model instance to mutate/populate
     * @return string[] List of error messages encountered during consumption (empty when none)
     */
    public function consumeData(mixed $data, AbstractCanvasPopulatedModel $model): array{
        if($data === null){
            return ["Cannot pass null data to emittion handler"];
        }
        $this->emitHandler->HandleEmitted($data, $model->getContext());
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