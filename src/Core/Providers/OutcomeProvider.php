<?php

namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\Outcome;
use CanvasApiLibrary\Core\Models\OutcomegroupStub;
use CanvasApiLibrary\Core\Models\OutcomeStub;
use CanvasApiLibrary\Core\Providers\Generated\Traits\OutcomeProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\OutcomeProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\OutcomeWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\ClientIDProvider;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * @implements OutcomeProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<Outcome>
 */
class OutcomeProvider extends AbstractProvider implements OutcomeProviderInterface{
    use OutcomeProviderProperties;
    use OutcomeWrapperTrait;

    public function __construct(
        CanvasCommunicator $canvasCommunicator,
        ClientIDProvider $clientIDProvider
    ) {
        parent::__construct( $canvasCommunicator,
        (new ModelPopulationConfigBuilder(Outcome::class))
        ->keyCopy("title")
        ->keyCopy("url")
        ->keyCopy("description")
        ->keyCopy("points_possible")
        ->keyCopy("mastery_points")
        ->keyCopy("calculation_method")
        ->keyCopy("calculation_int")->nullable()
        ,$clientIDProvider
        );
    }

    /**
     * Populates an outcome group
     * @param OutcomeStub $outcome
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcome>|UnauthorizedResult
     */
    public function populateOutcome(OutcomeStub $outcome, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        //use the url and domain to directly fetch the outcome group, so we don't have to know the context
        $url = $outcome->url;
        return $this->Get($url, $outcome->getContext());
    }

    /**
     * Gets all the outcomes in a given group
     * @param OutcomegroupStub $outcomeGroup
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Outcome[]>|UnauthorizedResult
     */
    public function getOutcomesInOutcomeGroup(OutcomegroupStub $outcomeGroup, bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        //Contains its own url, so we use that
        $url = $outcomeGroup->outcomes_url;
        return $this->GetMany($url, $outcomeGroup->getContext());
    }
}