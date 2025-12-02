<?php

namespace CanvasApiLibrary\Models\ContextPopulationTraits;
use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Models\Domain;

trait DomainIdentityTrait{
    public Domain $domain{
        get{
            return $this->domain;
        }
        set(Domain $value){
            if(isset($this->domain)){
                if($this->domain != $value){
                    throw new ChangingIdException("Tried to change the domain of a model.");
                }
                return;
            }
            $this->domain = $value;
        }
    }

    public int $id{
        get{
            return $this->id;
        }
        set (int $value){
            if(isset($this->id)){
                if($this->id != $value){
                    throw new ChangingIdException("Tried to change the id of a model what already has it's id set.");
                }
                return;
            }
            $this->id = $value;
        }
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
                $this->domain = $item;
                continue;
            }
        }
    }

    public function getContext(): array{
        return [$this, $this->domain];
    }

    public function getMinimumDataRepresentation(): mixed{
        return [
            self::class => $this->id,
            Domain::class => $this->domain->domain
        ];
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public static function newFromMinimumDataRepresentation(mixed $data):static{
        $item = new (self::class)();
        $item->id = $data[self::class];
        $item->domain = new Domain($data[Domain::class]);
        return $item;
    } 

    public function validateIdentityIntegrity() : bool{
        return isset($this->id) && isset($this->domain);
    }

    public function getUniqueId(): string{
        return static::class . "-" . $this->domain->domain . "-" . $this->id;
    }
}
