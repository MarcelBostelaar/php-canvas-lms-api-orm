<?php
namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\User;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;
use Exception;
use InvalidArgumentException;


/**
 * Provider for Canvas API User operations
 * 
 * @method Lookup<Models\Group, Models\User> getUsersInGroups() Virtual method to get all user in groups
 */
class UserProvider extends AbstractProvider{
    use UserProviderProperties;
    public function __construct(
        public readonly Services\StatusHandlerInterface $statusHandler
    ){}

    protected static $modelPopulator = 
    new ModelPopulationConfigBuilder(User::class)
    ->keyCopy("name");

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
        return $this->GetMany( "/sections/{$section->id}/enrollments&per_page=100$postfix", 
        $section->getContext(), self::$modelPopulator, fn($item) => $item["user"]);
    }

    public function populateUser(User $user): User{
        $this->Get("users/:id{$user->id}",
        $user->getContext(), self::$modelPopulator->withInstance($user));
        return $user;
    }
}