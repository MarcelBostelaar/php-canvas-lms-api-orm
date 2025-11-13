<?php

namespace CanvasApiLibrary\Models\ContextPopulationTraits;
use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Models\Domain;

trait DomainIdentityTrait{
    public Domain $domain{
        abstract protected set(Domain $value);
        abstract get;
    }

    abstract public int $id{
        get;
        set;
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
                    if($this->domain != $item){
                        throw new ChangingIdException("Tried to set the domain of a model that already exists.");
                    }
                    //same domain
                }
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

    public static function newFromMinimumDataRepresentation($data){
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
