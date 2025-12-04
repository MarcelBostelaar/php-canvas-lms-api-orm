<?php
namespace CanvasApiLibrary\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Models\User;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Models\Section;

interface UserProviderInterface extends HandleEmittedInterface{

    /**
    * @param User[] $users
    * @return User[]
    */
    public function populateUsers(array $users) : array;

    /**
    * @param Group[] $groups
    * @return Lookup<Group, User>
    */
    public function getUsersInGroups(array $groups) : Lookup;

    /**
    * @param Section[] $sections	 * @param ?string $enrollmentRoleFilter
    * @return Lookup<Section, User>
    */
    public function getUsersInSections(array $sections, ?string $enrollmentRoleFilter) : Lookup;

    /**
    
    * @return CanvasApiLibrary\Providers\Interfaces\UserProviderInterface
    */
    public function asAdmin() : CanvasApiLibrary\Providers\Interfaces\UserProviderInterface;

    /**
    * @param Course $course
    * @return CanvasApiLibrary\Providers\Interfaces\UserProviderInterface
    */
    public function withinCourse(Course $course) : CanvasApiLibrary\Providers\Interfaces\UserProviderInterface;

    /**
    * @param Group $group
    * @return mixed
    */
    public function getUsersInGroup(Group $group) : mixed;

    /**
    * @param Section $section	 * @param ?string $enrollmentRoleFilter
    * @return mixed
    */
    public function getUsersInSection(Section $section, ?string $enrollmentRoleFilter) : mixed;

    /**
    * @param User $user
    * @return User
    */
    public function populateUser(User $user) : User;

}
