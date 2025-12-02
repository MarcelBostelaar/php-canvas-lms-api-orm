<?php

namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\IdentityTraits\DomainBoundIdTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\GroupProperties;

/**
 * @property string $name
 */
final class Group extends AbstractCanvasPopulatedModel{
    use GroupProperties;
    use DomainBoundIdTrait;
    protected static array $properties = [["string", "name"]];

    public static array $plurals = ["Groups"];
}
