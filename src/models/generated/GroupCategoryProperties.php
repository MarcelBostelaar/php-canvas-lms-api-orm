<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;

trait GroupCategoryProperties{
    abstract public function getDomain(): Domain;

    public function getMinimumDataRepresentation(){
        if(!(            isset($this->id) &&
                        true
        )){
            throw new NotPopulatedException("Not all minimum required fields for this model, so it can be re-populated, have been set.");
        }
        return [
            ['id'] => $this->id        ];
    }

    public static function newFromMinimumDataRepresentation(Domain $domain, array $data): GroupCategory{
        if(!(            isset($data['id']) &&
                        true
        )){
            throw new NotPopulatedException("Not all minimum required fields for this model are in the data provided.");
        }
        $newInstance = new GroupCategory($domain);
                $this->id = $data['id'];
        
                return $newInstance;
    }
    }