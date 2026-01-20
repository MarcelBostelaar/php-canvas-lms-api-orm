<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\OutcomeStubProperties;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdWithUrlTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;

class OutcomeStub extends AbstractCanvasPopulatedModel{
    use DomainBoundIdWithUrlTrait;
    use OutcomeStubProperties;
    protected static array $properties = [
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["OutcomeStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
