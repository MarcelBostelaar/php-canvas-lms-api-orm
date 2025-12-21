<?php
namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\UserStub;
use CanvasApiLibrary\Core\Providers\Generated\Traits\UserProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\UserWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\User;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Services\ErrorOnNotFoundStatusHandlerWrapper;
use CanvasApiLibrary\Core\Services\StatusHandlerInterface;
use InvalidArgumentException;

use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;


/**
 * @implements UserProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<User>
 */
class UserProvider extends AbstractProvider implements UserProviderInterface{
    use UserProviderProperties;
    use UserWrapperTrait;

    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($canvasCommunicator,
        new ModelPopulationConfigBuilder(User::class)
                ->keyCopy("name"));
    }

    /**
     * @param Models\Group $group
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
    */
    public function getUsersInGroup(Models\Group $group, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->GetMany( "/groups/{$group->id}/users", $group->getContext());
    }

    /**
     * Gets all users in a section.
     * @param Models\Section $section
     * @param ?string $enrollmentRoleFilter Filter to only retrieve a specific type of user. Allowed values: Student, Teacher, Ta, Observer, Designer
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
     */
    public function getUsersInSection(Models\Section $section, ?string $enrollmentRoleFilter, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        $postfix = "";
        switch($enrollmentRoleFilter){
            case null:
                break;
            case "Student":
            case "Teacher":
            case "Ta":
            case "Observer":
            case "Designer":
                $postfix = "&role=$enrollmentRoleFilter" . "Enrollment";
                break;
            default:
                throw new InvalidArgumentException("Cannot pass $enrollmentRoleFilter as the role, must be null for no filtering, or one of following: Student, Teacher, Ta, Observer, Designer");
        }
        return $this->GetMany( "/sections/{$section->id}/enrollments?per_page=100$postfix", 
        $section->getContext(), 
        $this->modelPopulator
        ->staticFrom($section->course)->to("optionalCourseContext"),
        fn($item) => $item["user"],);
    }

    /**
     * Gets all users in a course.
     * @param Models\Course $course
     * @param ?string $enrollmentRoleFilter Filter to only retrieve a specific type of user. Allowed values: student, teacher, ta, observer, designer
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<User[]>|UnauthorizedResult
     */
    public function getUsersInCourse(Models\Course $course, ?string $enrollmentRoleFilter, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        $postfix = "";
        switch($enrollmentRoleFilter){
            case null:
                break;
            case "student":
            case "teacher":
            case "ta":
            case "observer":
            case "designer":
                $postfix = "&enrollment_type[]=$enrollmentRoleFilter";
                break;
            default:
                throw new InvalidArgumentException("Cannot pass $enrollmentRoleFilter as the role, must be null for no filtering, or one of following: student, teacher, ta, observer, designer");
        }
        return $this->GetMany( "/courses/{$course->id}/users?per_page=100$postfix", 
        $course->getContext()); //optional course context already handled through context system.
    }

    /**
     * @param Domain $domain
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<User>|UnauthorizedResult
     */
    public function getUserSelfInfo(Domain $domain, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->Get("/users/self", $domain->getContext());
    }

    /**
     * Populates a user from the canvas API.
     * @param Models\UserStub $user
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<User>|UnauthorizedResult
     */
    public function populateUser(UserStub $user, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        if($user->optionalCourseContext === null){
            //Must be retrieved from global route. Note that only admins can do this.
            /** @var User $result */
            return $this->Get("/users/{$user->id}",
            $user->getContext(), 
            null,
            null
        );
        }
        
        //Try to find the user within the course provided as the current context.
        return $this->Get("courses/{$user->optionalCourseContext->id}/users/{$user->id}",
            $user->getContext()
        );
    }
}