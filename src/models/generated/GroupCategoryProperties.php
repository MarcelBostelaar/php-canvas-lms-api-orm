<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\GroupCategory;

trait GroupCategoryProperties{
    public abstract Domain $domain{
        get;
        protected set(Domain $value);
    }
    
    abstract public function getMinimumDataRepresentation();
    abstract public static function newFromMinimumDataRepresentation(mixed $data): GroupCategory;
    }