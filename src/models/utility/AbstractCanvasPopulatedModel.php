<?php

namespace CanvasApiLibrary\Models\Utility;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Exceptions\ChangingIdException;

abstract class AbstractCanvasPopulatedModel implements ModelInterface{

    /**
     * Constructs a new basemodel. Do not override the constructor with non-optional parameters.
     * @param Domain $domain The domain of the canvas object
     */
    public function __construct(){
    }
    
    public Domain $domain{
        get{
            return $this->domain;
        }
        protected set(Domain $value){
            if(isset($this->domain)){
                throw new ChangingIdException("Tried to change the domain of a model.");
            }
            $this->domain = $value;
        }
    }

    public int $id{
        get{
            return $this->id;
        }
        set (int $value){
            if(isset($this->id)){
                throw new ChangingIdException("Tried to change the id of a model what already has it's id set.");
            }
            $this->id = $value;
        }
    }

    /**
     * A list of non-nullable property names to be generated, which are required to re-populate the item from the canvas api.
     * These properties are saved inside foreign models, if the model is included as a field in the other model. 
     * A [type, name] must be given.
     * @var array{0: class-string, 1: string}
     */
    protected static array $minimumProperties = [];
    /**
     * A list of non-nullable property names to be generated. 
     * A [type, name] must be given.
     * @var array{0: class-string, 1: string}
     */
    protected static array $properties = [];
    /**
     * A list of nullable property names to be generated. 
     * A [type, name] must be given.
     * @var array{0: class-string, 1: string}
     */
    protected static array $nullableProperties = [];
}