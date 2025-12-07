<?php

namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalCourseContextTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalUserContextTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Generated\GroupProperties;

/**
 * @property string $name
 */
final class Group extends AbstractCanvasPopulatedModel{
    use GroupProperties;
    use DomainBoundIdTrait;
    use OptionalUserContextTrait;
    use OptionalCourseContextTrait;
    protected static array $properties = [["string", "name"]];

    public static array $plurals = ["Groups"];
}
