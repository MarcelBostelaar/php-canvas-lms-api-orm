<?php

namespace CanvasApiLibrary\Core\Models\Utility;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\IdentityBoiletplateTrait;

abstract class AbstractCanvasPopulatedModel implements ModelInterface{
    use IdentityBoiletplateTrait;
    public function __construct(){
       $this->ensureIdentityInitialized();
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