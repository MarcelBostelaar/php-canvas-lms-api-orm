<?php
namespace CanvasApiLibrary\Services;
interface StatusHandlerInterface {
    /**
     * Handles status'.
     * @param mixed $data Data returned from the API
     * @param CanvasReturnStatus $status
     * @param string $message Optional message to include in error handling
     * @return mixed Returns the data if no error occurs, or new data if modified.
     */
    public function HandleStatus(mixed $data, Services\CanvasReturnStatus $status): mixed;
}