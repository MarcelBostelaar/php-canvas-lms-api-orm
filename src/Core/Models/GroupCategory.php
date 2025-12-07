<?php
namespace CanvasApiLibrary\Core\Models;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalCourseContextTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\Atomic\OptionalUserContextTrait;
use CanvasApiLibrary\Core\Models\IdentityTraits\DomainBoundIdTrait;
use CanvasApiLibrary\Core\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Core\Models\Generated\GroupCategoryProperties;

final class GroupCategory extends AbstractCanvasPopulatedModel{ 
    use GroupCategoryProperties;   
    use DomainBoundIdTrait;
    use OptionalCourseContextTrait;
    use OptionalUserContextTrait;
    public static array $plurals = ["GroupCategories"];
}