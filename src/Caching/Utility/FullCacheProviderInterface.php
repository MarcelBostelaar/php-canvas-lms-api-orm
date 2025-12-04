<?php

namespace CanvasApiLibrary\Caching\Utility;

use CanvasApiLibrary\Caching\Utility\CacheResult;

interface FullCacheProviderInterface{
    /**
     * Summary of get
     * @param CacheRule $rule
     * @param string $clientID
     * @param string $functionname
     * @param mixed[] $args
     * @return array{0: CacheResult, 1: callable} An array of the cache result and a function to set the cache value.
     */
    public function get(CacheRule $rule, string $clientID, string $functionname, mixed ...$args): array;
}