<?php
namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\ContextPopulationTraits\DomainIdentityTrait;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\GroupCategoryProperties;

final class GroupCategory extends AbstractCanvasPopulatedModel{ 
    use GroupCategoryProperties;   
    use DomainIdentityTrait;
    public static array $plurals = ["GroupCategories"];
}