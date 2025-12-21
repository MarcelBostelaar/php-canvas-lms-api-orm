<?php

namespace CanvasApiLibrary\Core\Providers\Traits;

use CanvasApiLibrary\Core\Providers\Interfaces\CourseProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\CourseProviderWrapper;
use Closure;

trait CourseWrapperTrait{
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
     * @return CourseProviderInterface<newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): CourseProviderInterface {
        return new CourseProviderWrapper( $this, $processor);
    }
}
