<?php
namespace CanvasApiLibrary\Core\Providers;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\Group;
use CanvasApiLibrary\Core\Providers\Generated\Traits\GroupProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\GroupProviderInterface;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;


/**
 * Provider for Canvas API group operations
 */
class GroupProvider extends AbstractProvider implements GroupProviderInterface{
    use GroupProviderProperties;

    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($canvasCommunicator,
            new ModelPopulationConfigBuilder(Group::class)->keyCopy("name"));
    }

    /**
     * @param Models\GroupCategory $category
     * @return Group[]
     */
    public function getAllGroupsInGroupCategory(Models\GroupCategory $category) : array{
        //Optional context already handled through getcontext
        return $this->GetMany( "/group_categories/$category->id/groups", 
        $category->getContext());
    }

    public function populateGroup(Group $group): Group{
        $this->Get( "/api/v1/groups/$group->id", $group->getContext(),
        $this->modelPopulator->withInstance($group));
        return $group;
    }
}