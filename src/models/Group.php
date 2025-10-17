<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\GroupProperties;

/**
 * @property string $name
 */
final class Group extends AbstractCanvasPopulatedModel{
    use GroupProperties;
    protected static array $properties = [["string", "name"]];

    public static array $plurals = ["Groups"];
}
