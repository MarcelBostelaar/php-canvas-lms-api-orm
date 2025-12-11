<?php
namespace CanvasApiLibrary\Core\Services;
interface StatusHandlerInterface {
    /**
     * Handles status'.
     * @param mixed $data Data returned from the API
     * @param CanvasReturnStatus $status
     * @return mixed Returns the data if no error occurs, or new data if modified.
     */
    public function HandleStatus(mixed $data, CanvasReturnStatus $status): mixed;
}