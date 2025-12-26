<?php

namespace CanvasApiLibrary\Core\Models;

use CanvasApiLibrary\Core\Models\Utility\ModelInterface;

final class Domain implements ModelInterface{
    public function __construct(public readonly string $domain){}

    public function getResourceKey(): string {
        return $this->domain;
    }

    public function getId(): string{
        return $this->domain;
    }

    public function populateWithContext(array $context){
        //No underlying context
    }

    public function validateIdentityIntegrity(): bool{
        return true; //No underlying identity
    }

    public function getContext(): array{
        return [$this];
    }

    public function withMetadataStripped(): ModelInterface{
        return $this;//no metadata
    }

    public static array $plurals = ["Domains"];
}