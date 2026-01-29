<?php

namespace CanvasApiLibrary\Core\Providers\Utility\Results;

use Closure;

trait ResultMonadTrait
{
    /**
     * Applies the given callback to the success value, if this is a SuccessResult.
     * @template TNewValue
     * @param Closure(mixed): TNewValue $callback Any transform 'a -> TNewValue'
     * @return ErrorResult|SuccessResult|UnauthorizedResult|ErrorResult A new Result, with the success value transformed if this was a SuccessResult
     */
    public function mapSuccess(Closure $callback): NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult{
        if(!$this instanceof SuccessResult){
            return $this;
        }
        $value = $this->value;
        return new SuccessResult($callback($value));
    }

    /**
     * Applies the given callback (which returns a successresult itself) to the success value, if this is a SuccessResult.
     * @template TNewValue
     * @param Closure(mixed): NotFoundResult|SuccessResult<TNewValue>|UnauthorizedResult|ErrorResult $callback Any transform 'a -> Result'
     * @return NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult A new Result, with the success value transformed if this was a SuccessResult
     */
    public function flatMapSuccess(Closure $callback): NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult{
        if(!$this instanceof SuccessResult){
            return $this;
        }
        $value = $this->value;
        return $callback($value);
    }

    /**
     * Executes the given callback if this is any kind of error result.
     * @template TNewValue
     * @param Closure(): NotFoundResult|SuccessResult<TNewValue>|UnauthorizedResult|ErrorResult $callback Any transform '() -> Result'
     * @return NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult A new Result, with the error handled if this was an error result
     */
    public function recoverAnyError(Closure $callback): NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult{
        if($this instanceof SuccessResult){
            return $this;
        }
        return $callback();
    }

    /**
     * Executes the given callback if this is of type NotFoundResult.
     * @template TNewValue
     * @param Closure(): NotFoundResult|SuccessResult<TNewValue>|UnauthorizedResult|ErrorResult $callback Any transform '() -> Result'
     * @return NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult A new Result, with the error handled if this was an error result
     */
    public function recoverNotFound(Closure $callback): NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult{
        if($this instanceof NotFoundResult){
            return $callback();
        }
        return $this;
    }
    
    /**
     * Executes the given callback if this is of type UnauthorizedResult.
     * @template TNewValue
     * @param Closure(): NotFoundResult|SuccessResult<TNewValue>|UnauthorizedResult|ErrorResult $callback Any transform '() -> Result'
     * @return NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult A new Result, with the error handled if this was an error result
     */
    public function recoverUnauthorized(Closure $callback): NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult{
        if($this instanceof UnauthorizedResult){
            return $callback();
        }
        return $this;
    }
    
    /**
     * Executes the given callback if this is of type ErrorResult.
     * @template TNewValue
     * @param Closure(): NotFoundResult|SuccessResult<TNewValue>|UnauthorizedResult|ErrorResult $callback Any transform '() -> Result'
     * @return NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult A new Result, with the error handled if this was an error result
     */
    public function recoverOtherError(Closure $callback): NotFoundResult|SuccessResult|UnauthorizedResult|ErrorResult{
        if($this instanceof ErrorResult){
            return $callback();
        }
        return $this;
    }
}