<?php
namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\IdentityTraits\DomainBoundIdTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\GroupCategoryProperties;

final class GroupCategory extends AbstractCanvasPopulatedModel{ 
    use GroupCategoryProperties;   
    use DomainBoundIdTrait;
    public static array $plurals = ["GroupCategories"];
}