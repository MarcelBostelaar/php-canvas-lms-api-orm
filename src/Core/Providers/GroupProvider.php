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
use CanvasApiLibrary\Core\Services\StatusHandlerInterface;


/**
 * Provider for Canvas API group operations
 */
class GroupProvider extends AbstractProvider implements GroupProviderInterface{
    use GroupProviderProperties;

    public function __construct(
        StatusHandlerInterface $statusHandler,
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($statusHandler, $canvasCommunicator,
            new ModelPopulationConfigBuilder(Group::class)->keyCopy("name"));
    }

    /**
     * @param Models\GroupCategory $category
     * @return Group[]
     */
    public function getAllGroupsInGroupCategory(Models\GroupCategory $category) : array{
        $builder = $this->modelPopulator;

        //Pass optional context to group
        $optionalCourse = $category->optionalCourseContext;
        $optionalUser = $category->optionalUserContext;

        if($optionalCourse !== null){
            $builder = $builder->staticFrom($optionalCourse)->to("optionalCourseContext");
        }
        if($optionalUser !== null){
            $builder = $builder->staticFrom($optionalUser)->to("optionalUserContext");
        }

        return $this->GetMany( "/group_categories/$category->id/groups", 
        $category->getContext(),
        $builder);
    }

    public function populateGroup(Group $group): Group{
        $this->Get( "/api/v1/groups/$group->id", $group->getContext(),
        $this->modelPopulator->withInstance($group));
        return $group;
    }
}