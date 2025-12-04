<?php

namespace CanvasApiLibrary\Caching\Utility;

use CanvasApiLibrary\Caching\Utility\CacheResult;

interface CacheStorageServiceInterface{
    public function set($key, $value);
    public function get($key): CacheResult;
}