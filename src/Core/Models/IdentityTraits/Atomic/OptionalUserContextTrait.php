<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;

use CanvasApiLibrary\Core\Exceptions\ChangingIdException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\User;
use GithubProjectViewer\Util\Caching\SetMetadata;


/**
 * Add this to models that can optionally be retrieved through a user, among other options,
 * but which does not intrinsically have a user as part of its identity.
 */
trait OptionalUserContextTrait{

    protected abstract function setMetadata(string $key, $value);
    protected abstract function getMetadata(string $key) : mixed;

    /**
     * Set this value if you are using this model in the context of user-level (global) operations.
     * Doing this allows the api tool and optional caching layers to work more effectively.
     * May be required if the API key does not have certain admin permissions, 
     * in which case the code will throw an exception.
     * Is set automatically if the item has been retrieved from a service using a user directly.
     */
    public ?User $optionalUserContext{
        get { 
            $data = $this->getMetadata("optionalusercontext");
            if($data === null){
                return null;
            }
            return User::newFromMinimumDataRepresentation($data, $this->getContext());
        }
        set (User $value) {
            $data = $this->getMetadata("optionalusercontext");
            if($data === null){
                if($this->domain != $value->domain){
                    $selfDomain = $this->domain->domain;
                    $otherDomain = $value->domain->domain;
                    throw new MixingDomainsException("Tried to save a User from domain '$otherDomain' to an item from domain '$selfDomain'.");
                }
                //same domain, allowed to save
                $this->setMetadata("optionalusercontext", $value->getMinimumDataRepresentation());
            }
            else{
                if($data != $value->getMinimumDataRepresentation()){
                    throw new ChangingIdException("Tried to change the user of this item");
                }
                //Same user, pass.
            }
        }
    }

    protected function initializeOptionalUserContext(): void {
        $this->contextProcessors[] = function($item) {
            if($item instanceof User){
                $this->optionalUserContext = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this->optionalUserContext];
    }
}