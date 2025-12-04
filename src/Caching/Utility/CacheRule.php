<?php

namespace CanvasApiLibrary\Caching\Utility;

abstract class CacheRule{
    private int $ttl;
    public function __construct(int $ttl) {
        $this->ttl = $ttl;
    }

    public function getTTL(){
        return $this->ttl;
    }

    /**
     * Perform rule-specific processing of the args. May be implemented whichever way to serve the needs of your specific caching strategy.
     * @param mixed[] $args
     * @return mixed
     */
    abstract public function processArgs(mixed ...$args): mixed;
}