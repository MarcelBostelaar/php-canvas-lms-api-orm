<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Providers\Generated\Traits;

use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Models\User;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Models\Section;

trait UserProviderProperties{
    abstract public function populateUser(User $user);
    
    /**
     * Array variant of populateUser
     * @param User[] $users
     * @return User[]
     */
    public function populateUsers(array $users): array{
        return array_map(fn($x) => $this->populateUser($x), $users);
    }

    abstract public function getUsersInGroup(Group $group) : array;
    
    /**
     * Summary of getUsersInGroups
     * @param Group[] $groups
     * @return Lookup<Group, User>
     */
    public function getUsersInGroups(array $groups): Lookup{
        $lookup = new Lookup();
        foreach($groups as $group){
            $lookup->add($group, $this->getUsersInGroup($group));
        }
        return $lookup;
    }

    abstract public function getUsersInSection(Section $section, ?string $enrollmentRoleFilter) : array;
    
    /**
     * Summary of getUsersInSections
     * @param Section[] $sections
     * @return Lookup<Section, User>
     */
    public function getUsersInSections(array $sections, ?string $enrollmentRoleFilter): Lookup{
        $lookup = new Lookup();
        foreach($sections as $section){
            $lookup->add($section, $this->getUsersInSection($section, $enrollmentRoleFilter));
        }
        return $lookup;
    }
}
