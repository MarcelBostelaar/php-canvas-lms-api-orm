<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Providers\Generated\Traits;

use CanvasApiLibrary;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Models\GroupCategory;

trait GroupProviderProperties{
    abstract public function populateGroup(Group $group);
    
    /**
     * Array variant of populateGroup
     * @param Group[] $groups
     * @return Group[]
     */
    public function populateGroups(array $groups): array{
        return array_map(fn($x) => $this->populateGroup($x), $groups);
    }

    abstract public function getAllGroupsInGroupCategory(GroupCategory $category) : array;
    
    /**
     * Summary of getAllGroupsInGroupCategories
     * @param GroupCategory[] $groupCategories
     * @return Lookup<GroupCategory, Group>
     */
    public function getAllGroupsInGroupCategories(array $groupCategories): Lookup{
        $lookup = new Lookup();
        foreach($groupCategories as $groupCategory){
            $lookup->add($groupCategory, $this->getAllGroupsInGroupCategory($groupCategory));
        }
        return $lookup;
    }
}
