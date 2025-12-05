<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;
use CanvasApiLibrary\Core\Models\Domain;

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

    protected function initializeDomainIdentity(): void {
        $this->contextProcessors[] = function($item) {
            if($item instanceof Domain){
                $this->domain = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this, $this->domain];

        $this->mdrGetters[] = fn() => [Domain::class => $this->domain->domain];

        $this->mdrSetters[] = function(&$item, $data) {
            $item->domain = new Domain($data[Domain::class]);
        };

        $this->integrityValidators[] = fn() => isset($this->domain);

        $this->uniqueIdParts[] = fn() => $this->domain->domain;
    }
}
