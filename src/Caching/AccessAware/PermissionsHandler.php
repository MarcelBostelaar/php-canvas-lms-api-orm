<?php

namespace CanvasApiLibrary\Caching\AccessAware;

/**
 * Class that is used to generate, check and filter permissions for this caching system. Internal, static.
 */
class PermissionsHandler{
    /**
     * Returns only those permissions that exist in the same context as the given permission.
     * Ie, student-bound items exist in the context of a domain name and course id. 
     * This method returns only those permissions which are for that same context, 
     * ie all other permissions to see students in that course on that domain.
     * @param string $context A permission to use as a filter.
     * @param string[] $permissions Permissions to filter
     * @return string[]
     */
    public static function filterOnContext(string $context, array $permissions): array{
        //TODO implement
    }
}