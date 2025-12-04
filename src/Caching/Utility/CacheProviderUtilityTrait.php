<?php

namespace CanvasApiLibrary\Caching\Utility;

use CanvasApiLibrary\Caching\Utility\CacheResult;

trait CacheProviderUtilityTrait{
    /**
     * Summary of get
     * @param CacheRule $rule
     * @param string $clientID
     * @param string $functionname
     * @param mixed[] $args
     * @return array{0: CacheResult, 1: callable} An array of the cache result and a function to set the cache value.
     */
    public function get(CacheRule $rule, string $clientID, string $functionname, mixed ...$args): array{
        $result = $this->internalGet($rule, $clientID, $functionname, ...$args);
        if($result->isCacheHit){
            return [$result, fn($item) => $item];
        }
        $closure = function($item) use($rule, $clientID, $functionname, $args){
                $this->internalSet($item, $rule, $clientID, $functionname, ...$args);
                return $item;
            };
        return [
            $result,
            $closure->bindTo($this)
        ];
    }

    abstract protected function internalGet(CacheRule $rule, string $clientID, string $functionname, mixed ...$args): CacheResult;
    abstract protected function internalSet(mixed $item, CacheRule $rule, string $clientID, string $functionname, mixed ...$args): CacheResult;
}