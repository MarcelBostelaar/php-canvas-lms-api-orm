<?php

namespace CanvasApiLibrary\Caching\Utility;

class CacheResult{
    public function __construct(public readonly mixed $value, public readonly bool $isCacheHit) {
    }
}