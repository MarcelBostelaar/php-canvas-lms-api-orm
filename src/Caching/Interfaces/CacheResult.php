<?php

namespace CanvasApiLibrary\Caching\Interfaces;

class CacheResult{
    public function __construct(public readonly mixed $value, public readonly bool $isCacheHit) {
    }
}