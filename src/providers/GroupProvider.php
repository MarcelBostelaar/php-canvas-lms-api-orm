<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;


/**
 * Provider for Canvas API group operations
 * 
 * @method Lookup<Models\GroupCategory, Models\Group> GetAllGroupsInGroupCategories() Virtual method to get all groups in group categories
 */
class GroupProvider extends AbstractProvider{
    use GroupProviderProperties;
    public function __construct(public readonly Services\StatusHandlerInterface $statusHandler){}

    /**
     * Summary of getAllGroupsInGroupCategory
     * @param \CanvasApiLibrary\Models\GroupCategory $category
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @return Models\Group[]
     */
    public function getAllGroupsInGroupCategory(Models\GroupCategory $category, Domain $domain) : array{
        return $this->Get($domain, "/group_categories/{$category->id}/groups");
    }
    
    protected function populateModel(Models\Domain $domain, $model, mixed $data): Models\Utility\AbstractCanvasPopulatedModel{
        //todo
    }

    public function populateGroup(Group $group){
        //todo
    }
}