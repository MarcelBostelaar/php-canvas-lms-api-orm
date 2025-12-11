<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Core\Models\Generated;

use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\User;

trait UserProperties{
    public string $name{
        get {
            return $this->name;
        }
        set(string $value) {
            $this->name = $value;
        }
    }

}