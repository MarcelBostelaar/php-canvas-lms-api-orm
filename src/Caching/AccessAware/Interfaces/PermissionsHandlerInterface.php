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
     * Returns a permission representing the client's own access
     * @param string $clientID
     * @return Permission
     */
    public static function clientPermission(string $clientID): mixed;

    /**
     * Returns the permission type for domain-user scope
     * @return PermissionType The domain-user permission type
     */
    public static function domainUserType(): mixed;
}