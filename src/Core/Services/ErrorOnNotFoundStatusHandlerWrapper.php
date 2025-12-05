<?php
namespace CanvasApiLibrary\Core\Services;

use Exception;
class ErrorOnNotFoundStatusHandlerWrapper implements StatusHandlerInterface {

    public function __construct(private readonly StatusHandlerInterface $wrap, private readonly string $message){
    }
    /**
     * Handles status'.
     * @param mixed $data Data returned from the API
     * @param CanvasReturnStatus $status
     * @param string $message Optional message to include in error handling
     * @return mixed Returns the data if no error occurs, or new data if modified.
     */
    public function HandleStatus(mixed $data, CanvasReturnStatus $status): mixed{
        if($status === CanvasReturnStatus::NOT_FOUND){
            throw new Exception($this->message);
        }
        return $this->wrap->HandleStatus($data, $status);
    }
}