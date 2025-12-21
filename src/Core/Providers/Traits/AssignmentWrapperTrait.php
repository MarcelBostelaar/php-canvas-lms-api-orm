<?php

namespace CanvasApiLibrary\Core\Providers\Traits;

use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\AssignmentProviderWrapper;
use Closure;
use oldSuccessT;

trait AssignmentWrapperTrait{
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
     * @return AssignmentProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): AssignmentProviderInterface {
        return new AssignmentProviderWrapper( $this, $processor);
    }
}