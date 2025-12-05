<?php

namespace CanvasApiLibrary\Caching\AccessAware;

/**
 * Class that is used to generate, check and filter permissions for this caching system. Internal, static.
 */
class CacheResult{
    public function __construct(public readonly mixed $value, public readonly bool $hit, public readonly bool $miss) {
    }
}