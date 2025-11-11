<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\User;

trait UserProperties{
    public abstract Domain $domain{
        get;
        protected set(Domain $value);
    }
    
    public string $name{
        get {
            return $this->name;
        }
        set(string $value) {
            $this->name = $value;
        }
    }

    abstract public function getMinimumDataRepresentation();
    abstract public static function newFromMinimumDataRepresentation(mixed $data): User;
    }