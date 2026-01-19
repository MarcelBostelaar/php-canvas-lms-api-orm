<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\OutcomeResultStubProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\CourseBoundIdTrait;

class OutcomeResultStub extends AbstractCanvasPopulatedModel{
    use CourseBoundIdTrait;
    use OutcomeResultStubProperties;
    protected static array $properties = [
        [UserStub::class, "user"],
        [OutcomeStub::class, "learning_outcome"],
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["OutcomeResultStubs"];
    protected function getClassName(): string{
        return $this::class;
    }
}
