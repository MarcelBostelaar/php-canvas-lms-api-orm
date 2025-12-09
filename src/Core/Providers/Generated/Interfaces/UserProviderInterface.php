<?php
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Core\Models\Group;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\User;

interface UserProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
    /**
    * @param User[] $users
    * @return User[]
    */
    public function populateUsers(array $users) : array;

    /**
    * @param Group[] $groups
    * @return Lookup<Group, Group>
    */
    public function getUsersInGroups(array $groups) : Lookup;

    /**
    * @param Section[] $sections	 * @param ?string $enrollmentRoleFilter
    * @return Lookup<Section, Section>
    */
    public function getUsersInSections(array $sections, ?string $enrollmentRoleFilter) : Lookup;

    /**
    * @param Course[] $courses	 * @param ?string $enrollmentRoleFilter
    * @return Lookup<Course, Course>
    */
    public function getUsersInCourses(array $courses, ?string $enrollmentRoleFilter) : Lookup;

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
    * @param Course $course	 * @param ?string $enrollmentRoleFilter
    * @return mixed
    */
    public function getUsersInCourse(Course $course, ?string $enrollmentRoleFilter) : mixed;

    /**
    * @param User $user
    * @return User
    */
    public function populateUser(User $user) : User;

}
