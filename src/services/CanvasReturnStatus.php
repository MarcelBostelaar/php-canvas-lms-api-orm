<?php

namespace CanvasApiLibrary\Services;

enum CanvasReturnStatus: string {
    case SUCCESS = "success";
    case NOT_FOUND = "not found";
    case ERROR = "error";

    public function combineWith(CanvasReturnStatus $otherStatus): CanvasReturnStatus{
        if($this == $otherStatus){
            return $this;
        }
        if($this === CanvasReturnStatus::SUCCESS){
            return $otherStatus;
        }
        if($this === CanvasReturnStatus::NOT_FOUND){
            return $this;
        }
        if($otherStatus === CanvasReturnStatus::NOT_FOUND){
            return $otherStatus;
        }
        return CanvasReturnStatus::ERROR;
    }
}