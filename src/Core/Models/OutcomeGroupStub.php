<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\OutcomegroupStubProperties;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdWithUrlTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;

class OutcomegroupStub extends AbstractCanvasPopulatedModel{
    use DomainBoundIdWithUrlTrait;
    use OutcomegroupStubProperties;
    protected static array $properties = [
        //Can be added to base stub version because they are never at risk of being cache invalid or containing sensitive info
        ["string", "subgroups_url"],
        ["string", "outcomes_url"]
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["OutcomegroupStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
