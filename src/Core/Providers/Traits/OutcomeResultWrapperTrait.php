<?php

namespace CanvasApiLibrary\Core\Providers\Traits;

use CanvasApiLibrary\Core\Providers\Interfaces\OutcomeResultProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\OutcomeResultProviderWrapper;
use Closure;

trait OutcomeResultWrapperTrait{
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
     * @return OutcomeResultProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): OutcomeResultProviderInterface {
        return new OutcomeResultProviderWrapper( $this, $processor);
    }
}
