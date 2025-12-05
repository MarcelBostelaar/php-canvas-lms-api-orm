<?php
namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Providers\Generated\Traits\UserProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\User;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Services\ErrorOnNotFoundStatusHandlerWrapper;
use CanvasApiLibrary\Core\Services\StatusHandlerInterface;
use Exception;
use InvalidArgumentException;


/**
 * Provider for Canvas API User operations. 
 * Can be run in admin mode, enabling global operations for users, provided your API key has rights to do so.
 * Otherwise must be provided with the course that will be used as the context of the user operations, such as reading personal data.
 */
class UserProvider extends AbstractProvider implements UserProviderInterface{
    use UserProviderProperties;

    private bool $asAdminBool = false;
    private ?Course $currentCourseContext;

    public function __construct(
        StatusHandlerInterface $statusHandler,
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($statusHandler, $canvasCommunicator,
        new ModelPopulationConfigBuilder(User::class)
                ->keyCopy("name"));
    }

    /**
     * Enables admin mode on this provider. This enables access to admin-only global user endpoints.
     * Returns new user provider, does not modify original.
     * @return UserProviderInterface
     */
    public function asAdmin(): UserProviderInterface{
        $newProvider = new UserProvider($this->statusHandler, $this->canvasCommunicator);
        $newProvider->asAdminBool = true;
        return $newProvider;
    }

    /**
     * Makes provider work within the context of a specific course. Required to retrieve users if your API key does not have full global admin rights.
     * Returns new user provider, does not modify original.
     * @param Course $course The course to set the current context to.
     * @return UserProviderInterface
     */
    public function withinCourse(Course $course): UserProviderInterface{
        $newProvider = new UserProvider($this->statusHandler, $this->canvasCommunicator);
        $newProvider->currentCourseContext = $course;
        return $newProvider;
    }

    public function getUsersInGroup(Models\Group $group): array{
        return $this->GetMany( "/groups/{$group->id}/users", $group->getContext());
    }

    /**
     * Gets all users in a section.
     * @param Models\Section $section
     * @param mixed $enrollmentRoleFilter Filter to only retrieve a specific type of user. Allowed values: Student, Teacher, Ta, Observer, Designer
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
        $section->getContext(), $this->modelPopulator, fn($item) => $item["user"]);
    }

    /**
     * Populates a user from the canvas API.
     * @param Models\User $user
     * @throws Exception Exception if run in normal mode without a course set as context.
     * @throws Exception Exception if run in admin mode without admin permissions for the API key.
     * @return Models\User
     */
    public function populateUser(User $user): User{
        if($this->asAdminBool){
            //Admins can view user info globally
            $this->Get("/users/{$user->id}",
            $user->getContext(), 
            $this->modelPopulator->withInstance($user),
            null,
                //Wrap the handler for this specific call to throw an explanatory exception with a message on 404 not found instead.
            new ErrorOnNotFoundStatusHandlerWrapper($this->statusHandler, "Api route returned not found. Admin mode is enabled on the user provider. Ensure that your API key has admin rights in your Canvas LMS api environment.")
        );
            return $user;
        }
        if($this->currentCourseContext === null){
            throw new Exception("User provider is not set to admin mode, and no course context is set. Cannot retrieve users globally with admin rights, please provide either a course in which to find this user or enable admoin mode.");
        }
        
        //Try to find the user within the course provided as the current context.
        $this->Get("courses/{$this->currentCourseContext->id}/users/{$user->id}",
            $user->getContext(), 
            $this->modelPopulator->withInstance($user)
        );
        return $user;
    }
}