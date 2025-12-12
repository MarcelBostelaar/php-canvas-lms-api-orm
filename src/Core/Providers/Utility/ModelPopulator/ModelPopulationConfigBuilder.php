<?php

namespace CanvasApiLibrary\Core\Providers\Utility\ModelPopulator;

use CanvasApiLibrary\Core\Models\Utility\ModelInterface;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;
use DateTime;
use Exception;
use LogicException;

/**
 * @template TModel of AbstractCanvasPopulatedModel
 */
class ModelPopulationConfigBuilder{
    /**
     * @var array<int, array{0: ?ProviderInterface, 1: ProcessorInterface[], 2: ?ConsumerInterface}>
     */
    private array $instructions;
    
    /**
     * @var class-string<TModel>
     */
    private string $model;

    /**
     * @var TModel|null
     */
    private ?AbstractCanvasPopulatedModel $instance = null;

    /**
     * @param class-string<TModel> $model
     * @param array<int, array{0: ?ProviderInterface, 1: ProcessorInterface[], 2: ?ConsumerInterface}> $instructions
     */
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

    /**
     * @return array<int, array{0: ProviderInterface, 1: ProcessorInterface[], 2: ConsumerInterface}>
     */
    protected function getInstructionsFinished(): array{
        $stepped = $this->newInstruction();
        $lastInstruction = end($stepped->instructions);
        if($lastInstruction[0] === null & $lastInstruction[1] === [] & $lastInstruction[2] === null){
            //last instruction is empty, so final command was complete
            $popped = $stepped->instructions;
            array_pop($popped);
            return $popped;
        }
        throw new LogicException("Tried to get finalized instructions, but last build instruction is incomplete");
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
     * @param \CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface $emitHandler
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

    /**
     * Sets a specific instance to run the builder with. Method does not mutate original builder (entire builder is immutable/behaviourally pure).
     * @param \CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel $instance
     * @return ModelPopulationConfigBuilder
     */
    public function withInstance(AbstractCanvasPopulatedModel $instance): ModelPopulationConfigBuilder{
        $new = clone $this;
        $new->instance = $instance;
        return $new;
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
        return $this->addProcessor(new ProcessorClosure(fn($x) => 
                                                $x === null ? null : new DateTime($x)));
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

    /**
     * Builds the item
     * @param array<string, mixed> $data Dictionary data
     * @param ModelInterface ...$context 
     * @return TModel
     */
    public function build(array $data, ModelInterface ...$context) : AbstractCanvasPopulatedModel{
        $errors = [];

        $toPopulate = $this->instance === null ? new ($this->model)() : $this->instance;
        
        $toPopulate->id = $data["id"];
        $toPopulate->populateWithContext($context);
        array_push($context, $toPopulate); //add self to context in case any dependent items need this model as an identity element.

        $instructions = $this->getInstructionsFinished(); //checks if all instructions are finished, and returns finalized instructions.

        foreach($instructions as $instruction){
            [$provider, $processors, $consumer] = $instruction;
            array_push($errors, ...$this->buildOneInstruction($toPopulate, $data, $context, $provider, $processors, $consumer));
        }

        if(count($errors) > 0){
            throw new Exception(implode("\n", $errors));
        }

        return $toPopulate;
    }

    /**
     * @param array<int, array<string, mixed>> $dataArray
     * @param ModelInterface ...$context
     * @return TModel[]
     */
    public function buildMany(array $dataArray, ModelInterface ...$context) : array{
        if($this->instance !== null){
            throw new LogicException("Cannot build many with a pre-set instance.");
        }
        $results = [];
        foreach($dataArray as $data){
            $results[] = $this->build($data, ...$context);
        }
        return $results;
    }

    /**
     * Summary of buildOneInstruction
     * @param AbstractCanvasPopulatedModel $toBuild
     * @param array<string, mixed> $data
     * @param ModelInterface[] $context
     * @param ProviderInterface $provider
     * @param ProcessorInterface[] $processors
     * @param ConsumerInterface $consumer
     * @return string[] Errors that have come up
     */
    private static function buildOneInstruction(AbstractCanvasPopulatedModel $toBuild, array $data, array $context, ProviderInterface $provider, array $processors, ConsumerInterface $consumer): array{
        $errors = [];
        ['data' => $item, 'errors' => $error] = $provider->getData($data);
        array_push($errors, ...$error);

        foreach($processors as $processor){
            ["data" => $item, "errors" => $error, "continue" => $continue] = $processor->process($item, $context);
            array_push($errors, ...$error);
            if(!$continue){
                //Processor indicated not to continue processing this value.
                return $errors;
            }
        }

        if(!$consumer->getAcceptsNull()){
            if($item === null){
                $description = $provider->getDescription();
                $errors[] = "Unacceptable null value encountered for $description while processing data to build a " . $toBuild::class;
                return $errors; //Do not continue processing this value.
            }
        }
        $error = $consumer->consumeData($item, $toBuild, $context);
        if($error == []){
            return []; //earlier errors did not prevent finishing of instruction, return no errors.
        }
        array_push($errors, ...$error);
        return $errors;
    }
}
