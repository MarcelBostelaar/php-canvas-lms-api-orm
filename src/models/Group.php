<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;

/**
 * @property string $name
 */
final class Group extends AbstractCanvasPopulatedModel{
    protected static array $properties = [["string", "name"]];

    public static array $plurals = ["Groups"];
}
