<?php

namespace CanvasApiLibrary\Models\Utility;

interface ModelInterface{
    public function getUniqueId() : mixed;
    
    /**
     * Return all plural names for the model, used for dynamic multi-getters.
     * @return string[]
     */
    public static function getPluralNames(): array;
}