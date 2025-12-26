<?php

namespace CanvasApiLibrary\Caching\AccessAware\Interfaces;

use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\UserStub;

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
    public static function filterOnType(PermissionType $contextType, array $permissions) : array;

    public static function contextFrom(PermissionType $permission): ContextFilter;

    public static function contextFilterDomainCourseUser(CourseStub $course): ContextFilter;

    public static function contextFilterDomainCourse(CourseStub $course): ContextFilter;

    public static function contextFilterDomainUser(Domain $domain): ContextFilter;

    public static function contextFilterDomain(Domain $domain): ContextFilter;

    public static function domainPermission(Domain $domain): Permission;
    
    public static function domainCoursePermission(CourseStub $course): Permission;

    public static function domainUserPermission(UserStub $user): Permission;

    public static function domainCourseUserPermission(CourseStub $course, UserStub $user): Permission;

    public static function typeFromPermission(string $permission) : PermissionType;

    public static function typeFromContextFilter(string $contextFilter) : PermissionType;

    public static function domainType(): PermissionType;
    public static function domainCourseType(): PermissionType;
    public static function domainCourseUserType(): PermissionType;
    public static function domainUserType(): PermissionType;
    public static function globalType(): PermissionType;
}