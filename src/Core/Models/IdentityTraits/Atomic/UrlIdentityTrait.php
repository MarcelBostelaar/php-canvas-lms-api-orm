<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;

trait UrlIdentityTrait{
    public string $url{
        get{
            return $this->url;
        }
        set (string $value){
            if(isset($this->url)){
                if($this->url != $value){
                    throw new ChangingIdException("Tried to change the url id of a model what already has it's url id set.");
                }
                return;
            }
            //cut off "/api/v1/" if it's there
            if (str_starts_with($value, "/api/v1/")) {
                $value = substr($value, 8);
            }
            $this->url = $value;
        }
    }

    abstract protected function getClassName(): string;

    protected function initializeUrlIdentity(): void {
        $this->mdrGetters[] = fn() => ["urlid" => $this->url];

        $this->mdrSetters[] = function(&$item, $data) {
            $item->url = $data["urlid"]; 
        };

        $this->integrityValidators[] = fn() => isset($this->url);

        $this->resourceKeyParts[] = fn() => "urlID-" . $this->url;
    }
}
