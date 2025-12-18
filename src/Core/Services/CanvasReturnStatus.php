<?php

namespace CanvasApiLibrary\Core\Services;

enum CanvasReturnStatus: string {
    case SUCCESS = "success";
    case NOT_FOUND = "not found";
    case UNAUTHORIZED = "unauthorized";
    case ERROR = "error";

    public function combineWith(CanvasReturnStatus $otherStatus): CanvasReturnStatus{
        if($this == $otherStatus){
            return $this;
        }
        if($this === CanvasReturnStatus::SUCCESS){
            return $otherStatus;
        }
        if($this === CanvasReturnStatus::NOT_FOUND || $this === CanvasReturnStatus::UNAUTHORIZED){
            return $this;
        }
        if($otherStatus === CanvasReturnStatus::NOT_FOUND || $otherStatus === CanvasReturnStatus::UNAUTHORIZED){
            return $otherStatus;
        }
        return CanvasReturnStatus::ERROR;
    }
}