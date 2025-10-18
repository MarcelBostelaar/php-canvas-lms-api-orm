<?php

namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Providers\Utility\Lookup;

use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\GroupCategory;

trait TestTrait{
    abstract public function populateAssignment(Assignment $assignment);

    /**
     * Array variant of populateAssignment
     * @param Assignment[] $assignments
     * @return Assignment[]
     */
    public function populateAssignments(array $assignments): array{
        return array_map(fn($x) => $this->populateAssignment($x), $assignments);
    }

    abstract public function getAllAssignmentsInGroupCategory(GroupCategory $groupCategory) : array;
    /**
     * Summary of getAllAssignmentsInGroupCategories
     * @param GroupCategory[] $groupCategories
     * @return Lookup<GroupCategory, Assignment>
     */
    public function getAllAssignmentsInGroupCategories(array $groupCategories): Lookup{
        $lookup = new Lookup();
        foreach($groupCategories as $groupCategory){
            $lookup->add($groupCategory, $this->getAllAssignmentsInGroupCategory($groupCategory));
        }
        return $lookup;
    }
}