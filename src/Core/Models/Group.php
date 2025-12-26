<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\Generated\GroupProperties;

/**
 * @property string $name
 */
final class Group extends GroupStub{
    use GroupProperties;
    protected static array $properties = [["string", "name"]];

    public static array $plurals = ["Groups"];
    protected function getClassName(): string{
        return $this::class;
    }
}
