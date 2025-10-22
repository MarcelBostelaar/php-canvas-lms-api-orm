<?php

namespace CanvasApiLibrary\Providers\Utility;
use CanvasApiLibrary\Models\Domain;

interface HandleEmittedInterface{
    public function HandleEmitted(mixed $data, Domain $domain);
}