<?php

namespace CanvasApiLibrary\Core\Providers\Traits;

use CanvasApiLibrary\Core\Providers\Interfaces\OutcomeProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\OutcomeProviderWrapper;
use Closure;

trait OutcomeWrapperTrait{
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
     * @return OutcomeProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): OutcomeProviderInterface {
        return new OutcomeProviderWrapper( $this, $processor);
    }
}
