<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Core\Models\Generated;

use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\UserDisplay;

trait UserDisplayProperties{
    public string $short_name{
        get {
            return $this->short_name;
        }
        set(string $value) {
            $this->short_name = $value;
        }
    }

    public string $avatar_image_url{
        get {
            return $this->avatar_image_url;
        }
        set(string $value) {
            $this->avatar_image_url = $value;
        }
    }

    public string $html_url{
        get {
            return $this->html_url;
        }
        set(string $value) {
            $this->html_url = $value;
        }
    }

}