<?php
namespace CanvasApiLibrary\Models;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Generated\GroupCategoryProperties;

final class GroupCategory extends AbstractCanvasPopulatedModel{ 
    use GroupCategoryProperties;   
    public static array $plurals = ["GroupCategories"];
}