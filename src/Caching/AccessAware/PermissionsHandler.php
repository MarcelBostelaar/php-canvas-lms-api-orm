<?php

namespace CanvasApiLibrary\Caching\AccessAware;

use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\User;

/**
 * Class that is used to generate, check and filter permissions for this caching system. Internal, static.
 */
class PermissionsHandler{
    /**
     * Returns only those permissions that exist in the same context as the given filter.
     * Ie, student-bound items exist in the context of a domain name and course id. 
     * This method returns only those permissions which are for that same context, 
     * ie all other permissions to see students in that course on that domain.
     * @param string $context A context filter
     * @param string[] $permissions Permissions to filter
     * @return string[]
     */
    public static function filterOnContext(string $context, array $permissions): array{
        //TODO implement
    }

    /**
     * Filters a given list of permissions to only those of a certain type
     * @param int $contextType The context type to filter on.
     * @param string[] $permissions The permissions to filter
     * @return string[] Filtered permissions
     */
    public static function filterOnType(int $contextType, array $permissions) : array{
        //todo implement
    }

    public static function contextFrom(string $permission): string{
        //todo implement
    }

    public static function contextFilterDomainCourseUser(Course $course): string{
        //TODO implement
    }

    public static function contextFilterDomainCourse(Course $course): string{
        //TODO implement
    }

    public static function domainCoursePermission(Course $course): string{
        //todo implement
    }

    public static function domainCourseUserPermission(Course $course, User $user): string{
        //TODO implement
    }

    public static function typeFromPermissiom(string $permission) : int{
        //todo implement
    }

    public static function typeFromContextFilter(string $contextFilter) : int{
        //todo implement
    }
}