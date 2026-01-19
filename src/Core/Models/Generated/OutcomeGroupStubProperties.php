<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Core\Models\Generated;

use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\OutcomegroupStub;

trait OutcomegroupStubProperties{
    public string $subgroups_url{
        get {
            return $this->subgroups_url;
        }
        set(string $value) {
            $this->subgroups_url = $value;
        }
    }

    public string $outcomes_url{
        get {
            return $this->outcomes_url;
        }
        set(string $value) {
            $this->outcomes_url = $value;
        }
    }

}