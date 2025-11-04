<?php

namespace CanvasApiLibrary\Models\ContextPopulationTraits;
use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Models\Domain;

trait DomainTrait{
    public Domain $domain{
        abstract protected set(Domain $value);
        abstract get;
    }
    
    /**
     * Populates the model using the provided other models, filling in missing data.
     * @param ModelInterface[] $context A list of context items from which to pull the needed data to populate.
     * @return void
     * @throws ChangingIdException When already set context data is provided again
     * @throws NotPopulatedException When not all required fields are set
     */
    public function populateWithContext(array $context){
        foreach($context as $item){
            if($item instanceof Domain){
                if(isset($this->domain)){
                    //TODO check if domain is the same.
                    throw new ChangingIdException("Tried to set the domain of a model that already exists.");
                }
                $this->domain = $item;
                continue;
            }
        }
    }
}
