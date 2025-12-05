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

    public static function contextFrom(string $permission): string{
        //todo implement
    }

    public static function contextFilterUserbound(Course $course): string{
        //TODO implement
    }

    public static function contextFilterCoursebound(Course $course): string{
        //TODO implement
    }

    public static function coursePermission(Course $course): string{
        //todo implement
    }

    public static function userPermission(Course $course, User $user): string{
        //TODO implement
    }
}