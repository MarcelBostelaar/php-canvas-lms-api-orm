<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use Closure;

trait PrecallTrait{

    private Closure $preCacheCall;
    public function bindPreCacheMethod(Closure $func){
        $this->preCacheCall = $func;
    }

    protected function doPreCacheCall(){
        if($this->preCacheCall === null){
            return;
        }
        $this->preCacheCall();
    }
}