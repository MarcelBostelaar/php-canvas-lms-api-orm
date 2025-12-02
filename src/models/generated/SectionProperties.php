<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Section;

trait SectionProperties{
    public string $name{
        get {
            return $this->name;
        }
        set(string $value) {
            $this->name = $value;
        }
    }

}