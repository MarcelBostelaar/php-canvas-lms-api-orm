<?php

namespace CanvasApiLibrary\Core\Providers\Utility;

interface ClientIDProvider{
    /**Returns an id that identifies the current client uniquely */
    public function getClientID(): string;
}