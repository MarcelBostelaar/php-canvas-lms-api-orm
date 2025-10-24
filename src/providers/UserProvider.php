<?php
namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\User;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;


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

    /**
     * Summary of getUsersInGroup
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @param \CanvasApiLibrary\Models\Group $group
     * @return User[]
     */
    public function getUsersInGroup(Domain $domain, Models\Group $group): array{
        return $this->Get($domain, "/groups/{$group->id}/users");
    }

    public function getUsersInSection(Domain $domain, Models\Section $section): array{
        return $this->Get($domain, 
        "/sections/{$section->id}/enrollments?type[]=UserEnrollment&per_page=100", 
        [], fn($x) => $x["user"]);
    }

    protected function populateModel(Models\Domain $domain, $model, mixed $data): Models\Utility\AbstractCanvasPopulatedModel{
        //todo
    }

    public function populateUser(User $user){
        //todo
    }
}