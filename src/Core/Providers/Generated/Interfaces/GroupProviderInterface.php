<?php
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Core\Models\Group;
use CanvasApiLibrary\Core\Models\GroupCategory;

interface GroupProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
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
