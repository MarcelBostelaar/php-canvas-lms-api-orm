<?php
namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Student;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;


/**
 * Provider for Canvas API Student operations
 * 
 * @method Lookup<Models\Group, Models\Student> getStudentsInGroups() Virtual method to get all student in groups
 */
class StudentProvider extends AbstractProvider{
    use StudentProviderProperties;
    public function __construct(
        public readonly Services\StatusHandlerInterface $statusHandler
    ){}

    /**
     * Summary of getStudentsInGroup
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @param \CanvasApiLibrary\Models\Group $group
     * @return Student[]
     */
    public function getStudentsInGroup(Domain $domain, Models\Group $group): array{
        return $this->Get($domain, "/groups/{$group->id}/users");
    }

    public function getStudentsInSection(Domain $domain, Models\Section $section): array{
        return $this->Get($domain, 
        "/sections/{$section->id}/enrollments?type[]=StudentEnrollment&per_page=100", 
        [], fn($x) => $x["user"]);
    }

    protected function populateModel(Models\Domain $domain, $model, mixed $data): Models\Utility\AbstractCanvasPopulatedModel{
        //todo
    }

    public function populateStudent(Student $student){
        //todo
    }
}