<?php

namespace CanvasApiLibrary\Models\Utility;
use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Models\Domain;
use ChangingIdException;
use Exception;

abstract class AbstractCanvasPopulatedModel implements ModelInterface{

    /**
     * Constructs a new basemodel. Do not override the constructor with non-optional parameters.
     * @param Domain $domain The domain of the canvas object
     */
    public function __construct(private readonly Domain $domain){
    }
    public function getUniqueId(): mixed {
        return $this->domain->domain . "-" . $this::class . "-" . $this->id;
    }
    
    protected function getDomain(): Domain{
        return $this->domain;
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
     * A list of property names to be dynamically generated/handled. 
     * Optionally, instead of a property name, a [type, name] can be given, which will be used for type checking on setting.
     * If an unpopulated property is accessed, a NotPopulatedException is thrown.
     * List is used to calculate whether or not a model is fully populated.
     * Use docstring to provide info about properties to tooling.
     * @var array<string|array>
     */
    protected static array $properties = [];
    /**
     * A list of property names to be dynamically generated/handled. 
     * Optionally, instead of a property name, a [type, name] can be given, which will be used for type checking on setting.
     * If an unpopulated property is accessed, null is returned.
     * List is used to calculate whether or not a model is fully populated.
     * Use docstring to provide info about properties to tooling.
     * @var array<string|array>
     * @var array
     */
    protected static array $nullableProperties = [];
}