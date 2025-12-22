<?php
namespace CanvasApiLibrary\Core\Providers;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\Group;
use CanvasApiLibrary\Core\Models\GroupCategoryStub;
use CanvasApiLibrary\Core\Models\GroupStub;
use CanvasApiLibrary\Core\Providers\Generated\Traits\GroupProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\GroupProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\GroupWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;


/**
 * @implements GroupProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<Group>
 */
class GroupProvider extends AbstractProvider implements GroupProviderInterface{
    use GroupProviderProperties;
    use GroupWrapperTrait;

    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($canvasCommunicator,
            new ModelPopulationConfigBuilder(Group::class)->keyCopy("name"));
    }

    /**
     * @param GroupCategoryStub $groupCategory
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<Group[]>|UnauthorizedResult
     */
    public function getAllGroupsInGroupCategory(GroupCategoryStub $groupCategory, bool $skipCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        //Optional context already handled through getcontext
        return $this->GetMany( "/group_categories/$groupCategory->id/groups", 
        $groupCategory->getContext());
    }

    /**
     * @param GroupStub $group
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<Group>|UnauthorizedResult
     */
    public function populateGroup(GroupStub $group, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->Get( "/api/v1/groups/$group->id", $group->getContext());
    }
}