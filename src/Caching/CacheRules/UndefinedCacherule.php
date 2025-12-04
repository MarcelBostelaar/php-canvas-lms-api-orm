<?php

namespace CanvasApiLibrary\Caching\CacheRules;

use CanvasApiLibrary\Caching\Utility\CacheRule;
use Exception;

class UndefinedCacherule extends CacheRule{
    public function __construct() {
        parent::__construct(-1);
    }

    public function getTTL(){
        throw new Exception("Undefined cache rule placeholder");
    }

    public function processArgs(mixed ...$args): mixed{
        throw new Exception("Undefined cache rule placeholder");
    }
}