<?php

namespace CanvasApiLibrary\Core\Providers\Traits;

use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderWrapper;
use Closure;

trait UserWrapperTrait{
    /**
     * Summary of handleResults
     * @template oldSuccessT
     * @template oldUnauthorizedT
     * @template oldNotFoundT
     * @template oldErrorT
     * @template newSuccessT
     * @template newUnauthorizedT
     * @template newNotFoundT
     * @template newErrorT
     * @param Closure(oldSuccessT|oldErrorT|oldNotFoundT|oldUnauthorizedT) : (newSuccessT|newErrorT|newNotFoundT|newUnauthorizedT) $processor
     * @return UserProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): UserProviderInterface {
        return new UserProviderWrapper( $this, $processor);
    }
}
