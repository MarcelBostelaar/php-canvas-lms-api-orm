<?php

namespace CanvasApiLibrary\Caching\AccessAware\Interfaces;

use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\UserStub;
use ContextFilter;
use Permission;

/**
 * Class that is used to generate, check and filter permissions for this caching system. Internal, static.
 * @template Permission
 * @template ContextFilter
 * @template PermissionType
 */
interface PermissionsHandlerInterface{
    /**
     * Returns only those permissions that exist in the same context as the given filter.
     * Ie, student-bound items exist in the context of a domain name and course id. 
     * This method returns only those permissions which are for that same context, 
     * ie all other permissions to see students in that course on that domain.
     * @param ContextFilter $context A context filter
     * @param Permission[] $permissions Permissions to filter
     * @return Permission[] Filtered permissions
     */
    public static function filterOnContext(mixed $context, array $permissions): array;

    /**
     * Filters a given list of permissions to only those of a certain type
     * @param PermissionType $contextType The context type to filter on.
     * @param Permission[] $permissions The permissions to filter
     * @return Permission[] Filtered permissions
     */
    public static function filterOnType(mixed $contextType, array $permissions) : array;

    /**
     * Extracts the context filter from a given permission
     * @param PermissionType $permission The permission to extract context from
     * @return ContextFilter The context filter
     */
    public static function contextFrom(mixed $permission): mixed;

    /**
     * Creates a context filter for domain, course, and user scope
     * @param CourseStub $course The course to create context filter for
     * @return ContextFilter The context filter for domain, course, and user
     */
    public static function contextFilterDomainCourseUser(CourseStub $course): mixed;

    /**
     * Creates a context filter for domain and course scope
     * @param CourseStub $course The course to create context filter for
     * @return ContextFilter The context filter for domain and course
     */
    public static function contextFilterDomainCourse(CourseStub $course): mixed;

    /**
     * Creates a context filter for domain and user scope
     * @param Domain $domain The domain to create context filter for
     * @return ContextFilter The context filter for domain and user
     */
    public static function contextFilterDomainUser(Domain $domain): mixed;

    /**
     * Creates a context filter for domain scope only
     * @param Domain $domain The domain to create context filter for
     * @return ContextFilter The context filter for domain
     */
    public static function contextFilterDomain(Domain $domain): mixed;

    /**
     * Creates a permission for domain scope
     * @param Domain $domain The domain to create permission for
     * @return Permission The domain permission
     */
    public static function domainPermission(Domain $domain): mixed;
    
    /**
     * Creates a permission for domain and course scope
     * @param CourseStub $course The course to create permission for
     * @return Permission The domain-course permission
     */
    public static function domainCoursePermission(CourseStub $course): mixed;

    /**
     * Creates a permission for domain and user scope
     * @param UserStub $user The user to create permission for
     * @return Permission The domain-user permission
     */
    public static function domainUserPermission(UserStub $user): mixed;

    /**
     * Creates a permission for domain, course, and user scope
     * @param CourseStub $course The course to create permission for
     * @param UserStub $user The user to create permission for
     * @return Permission The domain-course-user permission
     */
    public static function domainCourseUserPermission(CourseStub $course, UserStub $user): mixed;

    /**
     * Extracts the permission type from a permission string
     * @param Permission $permission The permission string to extract type from
     * @return PermissionType The permission type
     */
    public static function typeFromPermission(mixed $permission) : mixed;

    /**
     * Extracts the permission type from a context filter string
     * @param ContextFilter $contextFilter The context filter string to extract type from
     * @return PermissionType The permission type
     */
    public static function typeFromContextFilter(mixed $contextFilter) : mixed;

    /**
     * Returns the permission type for domain scope
     * @return PermissionType The domain permission type
     */
    public static function domainType(): mixed;
    
    /**
     * Returns the permission type for domain-course scope
     * @return PermissionType The domain-course permission type
     */
    public static function domainCourseType(): mixed;
    
    /**
     * Returns the permission type for domain-course-user scope
     * @return PermissionType The domain-course-user permission type
     */
    public static function domainCourseUserType(): mixed;
    
    /**
     * Returns the permission type for domain-user scope
     * @return PermissionType The domain-user permission type
     */
    public static function domainUserType(): mixed;
    
    /**
     * Returns the permission type for global scope
     * @return PermissionType The global permission type
     */
    public static function globalType(): mixed;
}