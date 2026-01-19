<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\OutcomeResultProperties;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\IdentityTraits\CourseBoundIdTrait;

class OutcomeResult extends OutcomeResultStub{
    use CourseBoundIdTrait;
    use OutcomeResultProperties;
    protected static array $properties = [
        ["int", "score"],
        [\DateTime::class, "submitted_or_assessed_at"]
    ];
    protected static array $nullableProperties = [
    ];

    public static array $plurals = ["OutcomeResults"];
    protected function getClassName(): string{
        return $this::class;
    }
}
