<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;

use Exception;

/**
 * Modular model identity system. Traits provide functionality for properties which function as identities for the models, 
 * and serialization and deserialization functionality to go along with it.
 * Identities which have dependencies on each other (such as a course which depends on the domain)
 * should be initialized in order. It is adviced to make ergonomic combined traits out of these component traits, 
 * and to declare the initialization in that, so make the models easier to comprehend.
 * 
 * This is the base trait providing the event-subscription mechanism for identity handling.
 * All specific identity traits (Domain, Course, Assignment, User, etc) register their
 * handlers with this base, which executes them in the order the traits are initialized.
 */
trait IdentityBoiletplateTrait {
    
    /** @var callable[] Functions that process context array to populate identity fields */
    protected array $contextProcessors = [];
    
    /** @var callable[] Functions that return context items for this model */
    protected array $contextGetters = [];
    
    /** @var callable[] Functions that return minimum data representation parts */
    protected array $mdrGetters = [];
    
    /** @var callable[] Functions that populate from minimum data representation */
    protected array $mdrSetters = [];
    
    /** @var callable[] Functions that validate identity integrity */
    protected array $integrityValidators = [];
    
    /** @var callable[] Functions that return unique ID parts */
    protected array $resourceKeyParts = [];
    
    /** @var bool Tracks if identity traits have been initialized */
    protected bool $identityInitialized = false;

    /**
     * Metadata storage. Is (must be) stripped out before storing item in cache.
     * @var array Associative array.
     */
    private array $metadata = [];

    /**
     * Sets a metadata value
     * @param string $key 
     * @param mixed $value Any value, not null.
     * @throws Exception Throws if value is set to null.
     * @return void
     */
    protected function setMetadata(string $key, $value){
        if($value === null){
            throw new Exception("Cannot pass null as a value to setMetadata");
        }
        $this->metadata[$key] = $value;
    }

    /**
     * Gets a metadata value
     * @param string $key
     * @return mixed The value. Null if value is not set.
     */
    protected function getMetadata(string $key) : mixed{
        if(isset($this->metadata[$key])){
            return $this->metadata[$key];
        }
        return null;
    }

    /**
     * Returns a clone of this model, with the metadata stripped from it. Call this before caching.
     * @return self
     */
    public function withMetaDataStripped(): self{
        $cloned = clone $this;
        $cloned->metadata = [];
        return $cloned;
    }
    
/**
     * Populates the model using the provided other models, filling in missing data.
     * Executes all registered context processors in order.
     * Because the properties that make up the model's identity are unchangable, 
     * this method at the same time checks against changing core identity information, 
     * and against the mixing of incompatible identity components (such as two different domains or courses).
     * 
     * To be used when creating a new model from just an id, within the same context as an existing model,
     * such as when reading the id in an api call of a parent/child item.
     * 
     * @param array $context A list of context items from which to pull the needed data to populate.
     * @return void
     * @throws \CanvasApiLibrary\Core\Exceptions\ChangingIdException When already set context data is provided again
     * @throws \CanvasApiLibrary\Core\Exceptions\NotPopulatedException When not all required fields are set
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
     * Returns all context items for this model, that is to say, all model properties that make up the model's identity.
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
    public static function newFromMinimumDataRepresentation(mixed $data, array $context): static {
        $item = new (static::class)();
        foreach ($item->mdrSetters as $setter) {
            $setter($item, $data);
        }
        $item->populateWithContext($context);
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
    public function getResourceKey(): string {
        $this->ensureIdentityInitialized();
        $parts = [];
        foreach ($this->resourceKeyParts as $partGetter) {
            $parts[] = $partGetter();
        }
        return implode('-', $parts);
    }

    /**
     * Called before each identity based action, ensures traits are loaded.
     */
    private function ensureIdentityInitialized(): void {
        if (!$this->identityInitialized) {
            $this->identityInitialized = true;
            $this->initIdentityTraits();
        }
    }

    /**
     * Creates an instance from a stub or supertype model.
     * 
     * @phpstan-param static $supertype
     * @return static
     */
    public static function fromStub(mixed $supertype): static{
        $mdr = $supertype->getMinimumDataRepresentation();
        $context = $supertype->getContext();
        return static::newFromMinimumDataRepresentation($mdr, $context);
    }
}
