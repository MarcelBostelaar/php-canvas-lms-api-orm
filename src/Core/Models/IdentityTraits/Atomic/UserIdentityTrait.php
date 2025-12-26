<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;
use CanvasApiLibrary\Core\Models\UserStub;

trait UserIdentityTrait{
    protected mixed $user_identity;
    public UserStub $user{
        get { 
            return UserStub::newFromMinimumDataRepresentation($this->user_identity, $this->getContext());
        }
        set (UserStub $value) {
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
            if($item instanceof UserStub){
                $this->user = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this->user];

        $this->mdrGetters[] = fn() => [UserStub::class => $this->user->id];

        $this->mdrSetters[] = function(&$item, $data) {
            $item->user = UserStub::newFromMinimumDataRepresentation($data, $this->getContext());
        };

        $this->integrityValidators[] = fn() => isset($this->user);

        $this->resourceKeyParts[] = fn() => "UserStub:" . $this->user->id;
    }
}
