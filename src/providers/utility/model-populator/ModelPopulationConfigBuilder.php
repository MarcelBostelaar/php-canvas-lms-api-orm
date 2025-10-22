<?php

namespace CanvasApiLibrary\Providers\Utility\ModelPopulator;

use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\Utility\HandleEmittedInterface;
use DateTime;
use LogicException;

class ModelPopulationConfigBuilder{
    private $instructions;
    private string $model;

    public function __construct(string $model, array $instructions = [[null, [], null]]){
        $this->instructions = $instructions;
        $this->model = $model;
    }

    //internal elemental functions

    protected function newInstruction(): ModelPopulationConfigBuilder{
        $currInstruction = end($this->instructions);
        if($currInstruction[0] === null || $currInstruction[2] === null){
            throw new LogicException("Unfinished model populator command. Consumer or provider is not set.");
        }
        return new ModelPopulationConfigBuilder($this->model, array_merge($this->instructions, [[null, [], null]]));
    }

    protected function setProvider(ProviderInterface $provider): ModelPopulationConfigBuilder{
        $currInstruction = end($this->instructions);
        if($currInstruction[0] !== null){
            $newInstructs = $this->newInstruction()->instructions;
        }
        else{
            $newInstructs = $this->instructions;
        }
        $newInstructs[count($newInstructs) - 1][0] = $provider;
        return new ModelPopulationConfigBuilder($this->model, $newInstructs);
    }

    protected function setConsumer(ConsumerInterface $consumer): ModelPopulationConfigBuilder{
        $newInstructs = $this->instructions;
        if(end($newInstructs)[2] !== null){
            throw new LogicException("Re-set consumer, cannot set two consumers.");
        }
        $newInstructs[count($newInstructs) - 1][2] = $consumer;
        return new ModelPopulationConfigBuilder($this->model, $newInstructs);
    }

    protected function addProcessor(ProcessorInterface $processor): ModelPopulationConfigBuilder{
        $newInstructs = $this->instructions;
        $newInstructs[count($newInstructs) - 1][1][] = $processor;
        return new ModelPopulationConfigBuilder($this->model, $newInstructs);
    }

    //Public facing setters, still elemental

    /**
     * Configure rule to accept or not accept null. If set to false (default) will throw if null value is passed.
     * @param mixed $acceptsNull
     * @return ModelPopulationConfigBuilder
     */
    public function nullable($acceptsNull = true): ModelPopulationConfigBuilder{
        $newInstructs = $this->instructions;
        $newInstructs[count($newInstructs) - 1][2]->setAcceptsNull($acceptsNull);
        return new ModelPopulationConfigBuilder($this->model, $newInstructs);
    }
    
    /**
     * Get data from this key in the source data
     * @param mixed $key
     * @return ModelPopulationConfigBuilder
     */
    public function from($key): ModelPopulationConfigBuilder{
        return $this->setProvider(new ProviderKey($key));
    }

    /**
     * Provide a static value as the source, instead of copying from the data.
     * @param mixed $value
     * @return ModelPopulationConfigBuilder
     */
    public function staticFrom(mixed $value): ModelPopulationConfigBuilder{
        return $this->setProvider(new ProviderStatic($value));
    }

    /**
     * Under which key to put the processed data in the model.
     * @param mixed $key
     * @return ModelPopulationConfigBuilder
     */
    public function to($key): ModelPopulationConfigBuilder{
        return $this->setConsumer(new ConsumerModelKey($key));
    }

    /**
     * Instead of putting this data into the model, pass it to a given emit handler.
     * @param \CanvasApiLibrary\Providers\Utility\HandleEmittedInterface $emitHandler
     * @return ModelPopulationConfigBuilder
     */
    public function emittingConsumer(HandleEmittedInterface $emitHandler): ModelPopulationConfigBuilder{
        return $this->setConsumer(new ConsumerEmitter($emitHandler));
    }

    /**
     * Process the data with an arbitrary closure. Will only be called if the value is not null. If value if null, null is just passed along to the next processor.
     * @param \Closure $closure
     * @return ModelPopulationConfigBuilder
     */
    public function processNonNullValue(\Closure $closure): ModelPopulationConfigBuilder{
        return $this->addProcessor(new ProcessorClosure(fn($x) => $x === null ? null : $closure($x) ));
    }

    /**
     * Process the data with an abirary closure. Will always be called, even with a null value. Use this for methods which emit a non-null value on null.
     * @param \Closure $closure
     * @return ModelPopulationConfigBuilder
     */
    public function processAnyValue(\Closure $closure): ModelPopulationConfigBuilder{
        return $this->addProcessor(new ProcessorClosure($closure));
    }

    //Usability combinations

    /**
     * Transform this value (an integer id) into a given model class.
     * @param class-string $modelClass
     * @return ModelPopulationConfigBuilder
     */
    public function asModel($modelClass): ModelPopulationConfigBuilder{
        return $this->addProcessor(new ProcessorModel($modelClass));
    }

    /**
     * Transform this value (a DateTime string) into a DateTime object.
     * @return ModelPopulationConfigBuilder
     */
    public function asDateTime(): ModelPopulationConfigBuilder{
        return $this->addProcessor(new ProcessorClosure(fn($x) => new DateTime($x)));
    }

    /**
     * Use the same key as the from and to.
     * @param string $key
     * @return ModelPopulationConfigBuilder
     */
    public function keyCopy($key): ModelPopulationConfigBuilder{
        return $this->from($key)->to($key);
    }

    //Building

    public function build(Domain $domain, $data) : mixed{
        //TODO: Orchestrate collected instructions
        return null;
    }
}
