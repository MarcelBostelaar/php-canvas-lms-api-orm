<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdOptionalUserCourseContextTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Generated\GroupProperties;

/**
 * @property string $name
 */
final class Group extends AbstractCanvasPopulatedModel{
    use GroupProperties;
    use DomainBoundIdOptionalUserCourseContextTrait;
    protected static array $properties = [["string", "name"]];

    public static array $plurals = ["Groups"];
}
