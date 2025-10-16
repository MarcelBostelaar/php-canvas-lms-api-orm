<?php

namespace CanvasApiLibrary\Services;

enum CanvasReturnStatus: string {
    case SUCCESS = "success";
    case NOT_FOUND = "not found";
    case ERROR = "error";
}