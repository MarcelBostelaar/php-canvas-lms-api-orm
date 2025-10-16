<?php

namespace CanvasApiLibrary\Providers\Utility;

/**
 * Functional implementation of a builder for defining transformation rules for canvas providers.
 */
class ModelPopulationConfigBuilder{
    private $instructions = [];
    private string $model;

    public function __construct(string $model, array $instructions = []){
        $this->instructions = $instructions;
        $this->model = $model;
    }

    public function direct(string $key): ModelPopulationConfigBuilder{
        return $this->keyToKey($key, $key);
    }

    public function keyToKey(string $fromKey, string $toKey): ModelPopulationConfigBuilder{
        return $this->keyToKeyWithTransformOrConstruct($fromKey, $toKey, fn($x) => $x);
    }

    public function directWithTransformOrConstruct(string $key, callable|string $transform): ModelPopulationConfigBuilder{
        return $this->keyToKeyWithTransformOrConstruct($key, $key, $transform);
    }

    public function keyToKeyWithTransformOrConstruct(string $fromKey, string $toKey, callable|string $transform): ModelPopulationConfigBuilder{
        if(is_string($transform)){
            if(!class_exists($transform)){
                throw new \InvalidArgumentException("Class $transform does not exist.");
            }
            $transform = fn($x) => new $transform($x);
        }
        //TODO
    }

    /**
     * Rule to configure the data in a key to be emitted to another provider for pre-population.
     * @param string $fromKey
     * @param string $toKey
     * @param \CanvasApiLibrary\Providers\Utility\AbstractProvider $provider
     * @return void
     */
    public function keyToModel(string $fromKey, AbstractProvider $provider): ModelPopulationConfigBuilder{
        //TODO
    }

    public function build(Domain $domain, $data) : mixed{
        //TODO
    }
}