<?php
namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdOptionalUserCourseContextTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Generated\GroupCategoryProperties;

final class GroupCategory extends AbstractCanvasPopulatedModel{ 
    use GroupCategoryProperties;
    use DomainBoundIdOptionalUserCourseContextTrait;
    public static array $plurals = ["GroupCategories"];
}