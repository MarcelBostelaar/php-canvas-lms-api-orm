<?php

namespace CanvasApiLibrary\Core\Providers;

use CanvasApiLibrary\Core\Models\CourseStub;
use CanvasApiLibrary\Core\Models\OutcomeResult;
use CanvasApiLibrary\Core\Models\OutcomeStub;
use CanvasApiLibrary\Core\Models\UserStub;
use CanvasApiLibrary\Core\Providers\Generated\Traits\OutcomeResultProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\OutcomeResultProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\OutcomeResultWrapperTrait;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\ClientIDProvider;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

/**
 * Does not support individual outcome population, as that is incredibly ineffiecient and has little use case.
 * @implements OutcomeResultProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<OutcomeResult>
 */
class OutcomeResultProvider extends AbstractProvider implements OutcomeResultProviderInterface{
    use OutcomeResultProviderProperties;
    use OutcomeResultWrapperTrait;

    public function __construct(
        CanvasCommunicator $canvasCommunicator,
        ClientIDProvider $clientIDProvider
    ) {
        parent::__construct( $canvasCommunicator,
        (new ModelPopulationConfigBuilder(OutcomeResult::class))
        ->keyCopy("score")
        ->keyCopy("submitted_or_assessed_at")->asDateTime()
        ->keyCopy("learning_outcome")->asModel(OutcomeStub::class)
        ->from("links")
            ->processAnyValue(fn($x) => $x["user"])
            ->to("user")
            ->asModel(UserStub::class)
        ,$clientIDProvider
        );
    }

    /**
     * Gets the total outcomes in a course. Can be filtered to specific users.
     * @param CourseStub $course
     * @param UserStub[] $users
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<UserStub, OutcomeResult>>|UnauthorizedResult
     */
    public function getOutcomesInCourse(CourseStub $course, array $users = [], bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $params = "";
        $userIDs = array_map(fn($user) => "user_ids[]=" . $user->getId(), $users);
        $params .= implode("&", $userIDs);
        $outcomes = $this->GetMany(
            "courses/{$course->id}/outcome_results" . (empty($params) ? "" : "?" . $params),
            $course->getContext()
        );
        if(!$outcomes instanceof SuccessResult){
            return $outcomes;
        }

        //group per user
        $data = $outcomes->value;
        $lookup = new Lookup();
        foreach($data as $outcomeResult){
            $lookup->add($outcomeResult->user, $outcomeResult);
        }
        //TODO figure out why this type error happens
        return new SuccessResult($lookup);
    }
}