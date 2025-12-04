<?php
namespace CanvasApiLibrary\Providers\Interfaces;

use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Models\GroupCategory;

interface GroupInterface{

    /**
    * @param Group[] $groups
    * @return Group[]
    */
    public function populateGroups(array $groups) : array;

    /**
    * @param GroupCategory[] $groupCategories
    * @return Lookup<GroupCategory, Group>
    */
    public function getAllGroupsInGroupCategories(array $groupCategories) : Lookup;

    /**
    * @param GroupCategory $category
    * @return mixed
    */
    public function getAllGroupsInGroupCategory(GroupCategory $category) : mixed;

    /**
    * @param Group $group
    * @return Group
    */
    public function populateGroup(Group $group) : Group;

}
