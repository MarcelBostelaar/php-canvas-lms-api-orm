<?php

namespace CanvasApiLibrary\Caching\Implementations\AccessAwareCaching\CacheRules;

use CanvasApiLibrary\Caching\Utility\CacheStorageServiceInterface;
use CanvasApiLibrary\Caching\Utility\FullCacheProviderInterface;
use CanvasApiLibrary\Caching\Utility\CacheProviderUtilityTrait;
use CanvasApiLibrary\Caching\Utility\CacheResult;
use CanvasApiLibrary\Caching\Utility\CacheRule;

class AccessAwareCacheProvider implements FullCacheProviderInterface{
    use CacheProviderUtilityTrait;

    public function __construct(private readonly CacheStorageServiceInterface $cacheStorage) {
    }

    private function makeKey(string $functionname, array $args): string{
        return hash('sha256', serialize([$functionname, $args]));
    }

    protected function internalGet(CacheRule $rule, string $clientID, string $functionname, mixed ...$args): CacheResult{
        $AccessGroupID = $rule->processArgs(...$args);
        

        $key = $this->makeKey($functionname, $args);

    }
    protected function internalSet(mixed $item, CacheRule $rule, string $clientID, string $functionname, mixed ...$args): CacheResult{

    }
}