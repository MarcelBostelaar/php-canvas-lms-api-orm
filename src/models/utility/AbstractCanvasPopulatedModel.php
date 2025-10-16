<?php

namespace CanvasApiLibrary\Models\Utility;
use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Models\Domain;

abstract class AbstractCanvasPopulatedModel implements ModelInterface{

    /**
     * Constructs a new basemodel. Do not override the constructor with non-optional parameters.
     * @param Domain $domain The domain of the canvas object
     * @param int $id The canvas ID of the object.
     */
    public function __construct(public readonly Domain $domain, public readonly int $id){
        $this->processProperties(static::$properties, false);
        $this->processProperties(static::$nullableProperties, false);
    }
    public function getUniqueId(): mixed {
        return $this->domain->domain . "-" . $this::class . "-" . $this->id;
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
    private array $virtualProperties = [];

    public function __get($name) {
        if (!array_key_exists($name, $this->virtualProperties)){
            $trace = debug_backtrace();
            $classname = get_class($this);
            trigger_error(
                "Undefined property on model '$classname' via __get(): " . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
        if($this->virtualProperties[$name]['value'] === null){
            if($this->virtualProperties[$name]['nullable']){
                return null;
            }
            throw new NotPopulatedException("Property $name has not been populated yet. Please populate the model first.");
        }
        if(is_a($this->virtualProperties[$name]['type'], AbstractCanvasPopulatedModel::class, true)){
            return new ($this->virtualProperties[$name]['type'])($this->domain, $this->virtualProperties[$name]['value']);
        }
        return $this->virtualProperties[$name]['value'];
    }

    public function __set($name, $value) {
        if (!array_key_exists($name, $this->virtualProperties)){
            $trace = debug_backtrace();
            $classname = get_class($this);
            trigger_error(
                "Undefined property on model '$classname' via __set(): " . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
        if($value === null){
            if(!$this->virtualProperties[$name]['nullable']){
                throw new \InvalidArgumentException("Property $name cannot be set to null.");
            }
            $this->virtualProperties[$name]['value'] = $value;
            return;
        }
        $type = $this->virtualProperties[$name]['type'];
        if($type !== null){
            if(!self::isA($value, $type)){
                throw new \InvalidArgumentException("Property $name must be of type $type, " . gettype($value) . " given.");
            }
        }
        if($value instanceof AbstractCanvasPopulatedModel){
            $value = $value->id;
        }
        $this->virtualProperties[$name]['value'] = $value;
    }

    /**
     * Used to check type equality, also when using primitives
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    private static function isA($value, string $type): bool {
        return match ($type) {
            'int' => is_int($value),
            'string' => is_string($value),
            'array' => is_array($value),
            'float' => is_float($value),
            'bool' => is_bool($value),
            'object' => is_object($value),
            default => $value instanceof $type,
        };
    }

    /**
     * Indicated whether or not the class is populated from the corresponding canvas system.
     * 
     * @var bool
     */
    public bool $isPopulated{
        get {
            foreach($this->virtualProperties as $_ => $info){
                if($info['value'] === null){
                    return false;
                }
            }
            return true;
        }
    }

    private function processProperties($propertyData, $nullable){//Set up virtual properties
        foreach($propertyData as $property){
            $type = null;
            $name = null;
            if(is_array($property)){
                if(count($property) !== 2){
                    throw new \InvalidArgumentException("Property definition with type must have exactly two elements: [type, name]");
                }
                $type = $property[0];
                $name = $property[1];
                if(!is_string($type) || !is_string($name)){
                    throw new \InvalidArgumentException("Property definitions must be either strings or [type, name] (both strings) arrays.");
                }
            } else {
                if(!is_string($property)){
                    throw new \InvalidArgumentException("Property definitions must be either strings or [type, name] arrays.");
                }
                $name = $property;
            }
            if(array_key_exists($name, $this->virtualProperties)){
                throw new \InvalidArgumentException("Property $name is defined multiple times in " . get_class($this) . ".");
            }
            $this->virtualProperties[$name] = [
                'type' => $type,
                'value' => null,
                'nullable' => $nullable
            ];
        }
    }
}