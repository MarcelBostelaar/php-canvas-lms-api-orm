<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;

trait NumberIdentityTrait{
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

    protected function initializeNumberIdentity(): void {
        $this->mdrGetters[] = fn() => [static::class => $this->id]; //TODO check if this still works with inheritance

        $this->mdrSetters[] = function(&$item, $data) {
            $item->id = $data[static::class]; //same here, should be stub class?
        };

        $this->integrityValidators[] = fn() => isset($this->id);

        $this->uniqueIdParts[] = fn() => static::class . "-" . $this->id; //TODO check if this works, should correctly show actual model class.
    }
}
