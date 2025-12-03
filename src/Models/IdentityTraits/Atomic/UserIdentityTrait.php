<?php

namespace CanvasApiLibrary\Models\IdentityTraits\Atomic;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Models\User;

trait UserIdentityTrait{
    protected mixed $user_identity;
    public User $user{
        get { 
            return User::newFromMinimumDataRepresentation($this->user_identity);
        }
        set (User $value) {
            if(!isset($this->user_identity)){
                if($this->domain != $value->domain){
                    $selfDomain = $this->domain->domain;
                    $otherDomain = $value->domain->domain;
                    throw new MixingDomainsException("Tried to save a User from domain '$otherDomain' to an item from domain '$selfDomain'.");
                }
                //same domain, allowed to save
                $this->user_identity = $value->getMinimumDataRepresentation();
            }
            else{
                if($this->user_identity != $value->getMinimumDataRepresentation()){
                    throw new ChangingIdException("Tried to change the user of this item");
                }
                //Same user, pass.
            }
        }
    }

    protected function initializeUserIdentity(): void {
        $this->contextProcessors[] = function($item) {
            if($item instanceof User){
                $this->user = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this->user];

        $this->mdrGetters[] = fn() => [User::class => $this->user->id];

        $this->mdrSetters[] = function(&$item, $data) {
            $item->user = User::newFromMinimumDataRepresentation($data);
        };

        $this->integrityValidators[] = fn() => isset($this->user);

        $this->uniqueIdParts[] = fn() => "User:" . $this->user->id;
    }
}
