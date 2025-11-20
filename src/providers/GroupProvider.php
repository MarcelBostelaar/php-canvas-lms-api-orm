<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Services\CanvasCommunicator;
use CanvasApiLibrary\Services\StatusHandlerInterface;


/**
 * Provider for Canvas API group operations
 * 
 * @method Lookup<Models\GroupCategory, Models\Group> GetAllGroupsInGroupCategories() Virtual method to get all groups in group categories
 */
class GroupProvider extends AbstractProvider{
    use GroupProviderProperties;

    public function __construct(
        public readonly StatusHandlerInterface $statusHandler,
        public readonly CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($statusHandler, $canvasCommunicator,
            new ModelPopulationConfigBuilder(Group::class)->keyCopy("name"));
    }

    /**
     * @param Models\GroupCategory $category
     * @return Group[]
     */
    public function getAllGroupsInGroupCategory(Models\GroupCategory $category) : array{
        return $this->GetMany( "/group_categories/$category->id/groups", $category->getContext());
    }

    public function populateGroup(Group $group): Group{
        $this->Get( "/api/v1/groups/$group->id", $group->getContext(),
        $this->modelPopulator->withInstance($group));
        return $group;
    }
}