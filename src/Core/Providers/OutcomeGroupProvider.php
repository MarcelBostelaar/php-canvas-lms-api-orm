<?php

namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Outcomegroup;
use CanvasApiLibrary\Core\Models\OutcomegroupStub;
use CanvasApiLibrary\Core\Providers\Generated\Traits\OutcomegroupProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\OutcomegroupProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\OutcomegroupWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\ClientIDProvider;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * @implements OutcomegroupProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<Outcomegroup>
 */
class OutcomegroupProvider extends AbstractProvider implements OutcomegroupProviderInterface{
    use OutcomegroupProviderProperties;
    use OutcomegroupWrapperTrait;

    public function __construct(
        CanvasCommunicator $canvasCommunicator,
        ClientIDProvider $clientIDProvider
    ) {
        parent::__construct( $canvasCommunicator,
        (new ModelPopulationConfigBuilder(Outcomegroup::class))
        ->keyCopy("title")
        ->keyCopy("url")
        ->keyCopy("description")->nullable()
        ->keyCopy("subgroups_url")
        ->keyCopy("outcomes_url")
        ->keyCopy("parent_outcome_group")
            ->nullable()
            ->asModel(OutcomegroupStub::class)
        ,$clientIDProvider
        );
    }

    /**
     * Populates an outcome group
     * @param OutcomegroupStub $outcomeGroup
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcomegroup>|UnauthorizedResult
     */
    public function populateOutcomegroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        //use the url and domain to directly fetch the outcome group, so we don't have to know the context
        $url = $outcomeGroup->url;
        return $this->Get($url, $outcomeGroup->getContext());
    }

    /**
     * Gets all outcome groups in a specified course
     * @param CourseStub $course
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcomegroup[]>|UnauthorizedResult
     */
    public function getOutcomegroupsInCourse(CourseStub $course, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->GetMany("/courses/$course->id/outcome_groups", $course->getContext());
    }

    /**
     * Returns all outcomes that are children of the given outcome group.
     * @param OutcomegroupStub $outcomeGroup
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcomegroup[]>|UnauthorizedResult
     */
    public function getSubgroupsOfOutcomegroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        $url = $outcomeGroup->subgroups_url;
        return $this->GetMany($url, $outcomeGroup->getContext());
    }
}