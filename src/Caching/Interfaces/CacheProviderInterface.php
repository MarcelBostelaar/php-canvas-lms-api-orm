<?php

namespace CanvasApiLibrary\Caching\Interfaces;

use CanvasApiLibrary\Caching\Interfaces\CacheResult;

/**
 * @template METADATA
 */
interface CacheProviderInterface{
    public function getCached(string $method, METADATA $metadata, array ...$arguments): CacheResult;
}