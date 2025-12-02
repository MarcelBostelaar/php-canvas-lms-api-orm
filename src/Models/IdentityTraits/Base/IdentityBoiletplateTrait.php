<?php

namespace CanvasApiLibrary\Models\IdentityTraits\Base;

use Vaimo\TopSort\Implementations\StringSort;

/**
 * Base trait providing the event-subscription mechanism for identity handling.
 * All specific identity traits (Domain, Course, Assignment, User) register their
 * handlers with this base, which executes them in the order traits are used.
 */
trait IdentityBoiletplateTrait {
    
    /** @var callable[] Functions that process context array to populate identity fields */
    private array $contextProcessors = [];
    
    /** @var callable[] Functions that return context items for this model */
    private array $contextGetters = [];
    
    /** @var callable[] Functions that return minimum data representation parts */
    private array $mdrGetters = [];
    
    /** @var callable[] Functions that populate from minimum data representation */
    private array $mdrSetters = [];
    
    /** @var callable[] Functions that validate identity integrity */
    private array $integrityValidators = [];
    
    /** @var callable[] Functions that return unique ID parts */
    private array $uniqueIdParts = [];
    
    /** @var bool Tracks if identity traits have been initialized */
    private bool $identityInitialized = false;
    
/**
     * Populates the model using the provided other models, filling in missing data.
     * Executes all registered context processors in order.
     * 
     * @param array $context A list of context items from which to pull the needed data to populate.
     * @return void
     * @throws \CanvasApiLibrary\Exceptions\ChangingIdException When already set context data is provided again
     * @throws \CanvasApiLibrary\Exceptions\NotPopulatedException When not all required fields are set
     */
    public function populateWithContext(array $context): void {
        $this->ensureIdentityInitialized();
        foreach ($this->contextProcessors as $processor) { //perform context population in order of trait dependency.
            foreach ($context as $index => $item) {
                if ($processor($item)) {
                    unset($context[$index]); //remove processed item
                    continue 2; // Go to next processor
                }
            }
        }
    }
    
    /**
     * Returns all context items for this model.
     * Collects results from all registered context getters.
     * 
     * @return array Array of model instances representing this model's context
     */
    public function getContext(): array {
        $this->ensureIdentityInitialized();
        $result = [];
        foreach ($this->contextGetters as $getter) {
            $contextItems = $getter();
            $result = [...$result, ...$contextItems];
        }
        return $result;
    }
    
    /**
     * Returns the minimum data needed to reconstruct this model's identity.
     * Merges results from all registered MDR getters.
     * 
     * @return array Associative array with class names as keys and IDs/identifiers as values
     */
    public function getMinimumDataRepresentation(): mixed {
        $this->ensureIdentityInitialized();
        $result = [];
        foreach ($this->mdrGetters as $getter) {
            $mdrPart = $getter();
            $result = [...$result, ...$mdrPart];
        }
        return $result;
    }
    
    /**
     * Creates a new instance from minimum data representation.
     * Executes all registered MDR setters to populate the instance.
     * 
     * @param mixed $data Minimum data representation array
     * @return static New instance populated with the provided data
     */
    public static function newFromMinimumDataRepresentation(mixed $data): static {
        $item = new (static::class)();
        foreach ($item->mdrSetters as $setter) {
            $setter($item, $data);
        }
        return $item;
    }
    
    /**
     * Validates that all required identity fields are set.
     * Returns true only if all registered validators pass.
     * 
     * @return bool True if identity is complete and valid
     */
    public function validateIdentityIntegrity(): bool {
        $this->ensureIdentityInitialized();
        foreach ($this->integrityValidators as $validator) {
            if (!$validator()) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Returns a unique string identifier for this model instance.
     * Concatenates results from all registered unique ID part generators.
     * 
     * @return string Unique identifier string
     */
    public function getUniqueId(): string {
        $this->ensureIdentityInitialized();
        $parts = [];
        foreach ($this->uniqueIdParts as $partGetter) {
            $parts[] = $partGetter();
        }
        return implode('-', $parts);
    }

    /**
     * Automatically discovers all initialize*Identity methods and their dependencies,
     * then calls them in topologically sorted order.
     */
    private function ensureIdentityInitialized(): void {
        if (!$this->identityInitialized) {
            $this->identityInitialized = true;
            $this->initIdentityTraits();
            
            //old code that used reflection to try and load traits in dependency order, doing it manually to avoid headaces
            // // Find all initialize*Identity methods (excluding *Dependencies methods)
            // $reflection = new \ReflectionClass($this);
            // $methods = $reflection->getMethods(\ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PUBLIC);
            
            // //Toplogical sorts dependencies so they are loaded in the correct order.
            // $initMethods = new StringSort();

            // foreach ($methods as $method) {
            //     $name = $method->getName();
            //     if (str_starts_with($name, 'initialize') 
            //         && str_ends_with($name, 'Identity')
            //         && !str_contains($name, 'Dependencies')) {
            //         $depMethod = $name . 'Dependencies';
            //         $dependencies = method_exists($this, $depMethod) ?
            //             $this->$depMethod() : [];
            //         $initMethods->add($name, $dependencies);
            //     }
            // }
            
            // $sorted = $initMethods->sort();
            
            // // Call each initialization method in dependency order
            // foreach ($sorted as $method) {
            //     $this->$method();
            // }
        }
    }
}
