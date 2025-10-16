<?php

namespace CanvasApiLibrary\Models;

use CanvasApiLibrary\Models\Utility\ModelInterface;

final class Domain implements ModelInterface{
    public function __construct(public readonly string $domain){}

    public function getUniqueId(): mixed {
        return $this->domain;
    }

    public function getPluralNames(): array{
        return ["Domains"];
    }
}