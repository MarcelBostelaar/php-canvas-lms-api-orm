<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheProviderInterface;
use CanvasApiLibrary\Caching\AccessAware\Interfaces\PermissionsHandlerInterface;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\CacheHelperTrait;
use CanvasApiLibrary\Caching\AccessAware\Providers\Traits\PermissionEnsurerTrait;
use CanvasApiLibrary\Core\Models\Outcome;
use CanvasApiLibrary\Core\Models\OutcomegroupStub;
use CanvasApiLibrary\Core\Models\OutcomeStub;
use CanvasApiLibrary\Core\Providers\OutcomeProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\OutcomeProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\OutcomeProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\OutcomeWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * @implements OutcomeProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 */
class OutcomeProviderCached implements OutcomeProviderInterface{

    use OutcomeProviderProperties;
    use PermissionEnsurerTrait;
    use OutcomeWrapperTrait;
    use CacheHelperTrait;

    
    public function __construct(
        private readonly OutcomeProvider $wrapped,
        private readonly CacheProviderInterface $cache,
        public readonly int $ttl,
        private readonly PermissionsHandlerInterface $permissionHandler
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getClientID(): string{
        return $this->wrapped->getClientID();
    }

    /**
     * Populates an outcome group
     * @param OutcomeStub $outcome
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcome>|UnauthorizedResult
     */
    public function populateOutcome(OutcomeStub $outcome, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->unknownPermissionSingleValue( //unknown permissions, if fetch fails, cache layer still tries to normally fetch, ensuring all those with permissions get the item
            Outcome::fromStub($outcome)->getResourceKey(),
            fn() => $this->wrapped->populateOutcome($outcome, $skipCache, $doNotCache),
            $skipCache,
            $doNotCache);
    }

    /**
     * Gets all the outcomes in a given group
     * @param OutcomegroupStub $outcomeGroup
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcome[]>|UnauthorizedResult
     */
    public function getOutcomesInOutcomeGroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        //All outcomes are visible if the group they are in is visible, so we can cache as a access agnostic collection with propagated permissions
        return $this->permissionPropagatedAccessAgnosticCollectionValue(
            "getOutcomesInOutcomeGroup" . $outcomeGroup->getResourceKey(),
            $outcomeGroup,
            fn() => $this->wrapped->getOutcomesInOutcomeGroup($outcomeGroup, $skipCache, $doNotCache),
            $skipCache,
            $doNotCache
        );
    }
}
