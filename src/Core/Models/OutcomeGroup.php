<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\OutcomegroupProperties;

final class Outcomegroup extends OutcomegroupStub{
    use OutcomegroupProperties;
    public static array $plurals = ["Outcomegroups"];

    protected static array $properties = [
        ["string", "title"],
        ["string", "description"]
    ];
    protected static array $nullableProperties = [
        [OutcomegroupStub::class, "parent_outcome_group"]
    ];

    protected function getClassName(): string{
        return $this::class;
    }
}