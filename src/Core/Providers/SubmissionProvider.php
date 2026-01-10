<?php

namespace CanvasApiLibrary\Core\Providers;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\AssignmentStub;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\Submission;
use CanvasApiLibrary\Core\Models\SubmissionStub;
use CanvasApiLibrary\Core\Models\User;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SubmissionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SubmissionProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\Traits\SubmissionWrapperTrait;
use CanvasApiLibrary\Core\Providers\UserProvider;
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
 * @implements SubmissionProviderInterface<SuccessResult,ErrorResult,NotFoundResult,UnauthorizedResult>
 * @extends parent<Submission>
 */
class SubmissionProvider extends AbstractProvider implements SubmissionProviderInterface{
    use SubmissionProviderProperties;
    use SubmissionWrapperTrait;
    
    public function __construct(
        CanvasCommunicator $canvasCommunicator,
        ClientIDProvider $clientIDProvider
    ) {
        parent::__construct($canvasCommunicator,
        (new ModelPopulationConfigBuilder(Submission::class))
                ->from("user_id")->to("user")->asModel(User::class)
                ->keyCopy("url")->nullable()
                ->keyCopy("submitted_at")->asDateTime()->nullable()
                ->keyCopy("section")->asModel(Section::class)->nullable(),
            $clientIDProvider);
    }

    /**
     * @param Models\AssignmentStub $assignment
     * @param ?UserProvider $userProvider If provided, will also fetch the users associated with these submissions and pass them to the emitted in the user provider.
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<Submission[]>|UnauthorizedResult
     */
    function getSubmissionsInAssignment(AssignmentStub $assignment, ?UserProviderInterface $userProvider = null, bool $skipCache = false) : ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        $postfix = "";
        $builder = $this->modelPopulator;
        if($userProvider !== null){
            $postfix = "?include[]=user";
            $builder = $builder->from("user")->emittingConsumer($userProvider);
        }
        $courseID = $assignment->course->id;
        return $this->GetMany("/courses/$courseID/assignments/$assignment->id/submissions$postfix", $assignment->getContext(),
            $builder
        );
    }

    /**
     * @param Models\SubmissionStub $submission
     * @param bool $skipCache Does nothing for this uncached base provider.
     * @return ErrorResult|NotFoundResult|SuccessResult<Submission>|UnauthorizedResult
     */
    public function populateSubmission(SubmissionStub $submission, bool $skipCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult{
        return $this->Get("/courses/{$submission->course->id}/assignments/{$submission->assignment->id}/submissions/{$submission->user->id}",
        $submission->getContext());
    }
}