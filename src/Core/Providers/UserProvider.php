<?php
namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Providers\Generated\Traits\UserProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\User;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Services\ErrorOnNotFoundStatusHandlerWrapper;
use CanvasApiLibrary\Core\Services\StatusHandlerInterface;
use InvalidArgumentException;


/**
 * Provider for Canvas API User operations. 
 * Can be run in admin mode, enabling global operations for users, provided your API key has rights to do so.
 * Otherwise must be provided with the course that will be used as the context of the user operations, such as reading personal data.
 */
class UserProvider extends AbstractProvider implements UserProviderInterface{
    use UserProviderProperties;

    public function __construct(
        StatusHandlerInterface $statusHandler,
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($statusHandler, $canvasCommunicator,
        new ModelPopulationConfigBuilder(User::class)
                ->keyCopy("name"));
    }

    public function getUsersInGroup(Models\Group $group): array{
        return $this->GetMany( "/groups/{$group->id}/users", $group->getContext());
    }

    /**
     * Gets all users in a section.
     * @param Models\Section $section
     * @param ?string $enrollmentRoleFilter Filter to only retrieve a specific type of user. Allowed values: Student, Teacher, Ta, Observer, Designer
     * @return User[]
     */
    public function getUsersInSection(Models\Section $section, ?string $enrollmentRoleFilter): array{
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
     * @return User[]
     */
    public function getUsersInCourse(Models\Course $course, ?string $enrollmentRoleFilter): array{
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
        $course->getContext(),
        $this->modelPopulator
        ->staticFrom($course)->to("optionalCourseContext"));
    }

    /**
     * Populates a user from the canvas API.
     * @param Models\User $user
     * @return Models\User
     */
    public function populateUser(User $user): User{
        if($user->optionalCourseContext === null){
            //Must be retrieved from global route. Note that only admins can do this.
            $this->Get("/users/{$user->id}",
            $user->getContext(), 
            $this->modelPopulator->withInstance($user),
            null,
                //Wrap the handler for this specific call to throw an explanatory exception with a message on 404 not found instead.
            new ErrorOnNotFoundStatusHandlerWrapper($this->statusHandler, "Api route returned not found. Ensure that your API key has admin rights in your Canvas LMS api environment, or provide this user with a course context to retrieve it from.")
        );
            return $user;
        }
        
        //Try to find the user within the course provided as the current context.
        $this->Get("courses/{$user->optionalCourseContext->id}/users/{$user->id}",
            $user->getContext(), 
            $this->modelPopulator->withInstance($user)
        );
        return $user;
    }
}