<?php

namespace CanvasApiLibrary\Core\Providers;
use CanvasApiLibrary\Core\Models as Models;
use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\Submission;
use CanvasApiLibrary\Core\Models\User;
use CanvasApiLibrary\Core\Providers\Generated\Traits\SubmissionProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\SubmissionProviderInterface;
use CanvasApiLibrary\Core\Providers\Interfaces\UserProviderInterface;
use CanvasApiLibrary\Core\Providers\UserProvider;
use CanvasApiLibrary\Core\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;



/**
 * Provider for Canvas API group operations
 * 
 * @method Lookup<Models\Assignment, Models\Submission> getSubmissionsForAssignments() Virtual method to get all groups in group categories
 */
class SubmissionProvider extends AbstractProvider implements SubmissionProviderInterface{
    use SubmissionProviderProperties;
    public function __construct(
        CanvasCommunicator $canvasCommunicator
    ) {
        parent::__construct($canvasCommunicator,
        new ModelPopulationConfigBuilder(Submission::class)
                ->from("user_id")->to("user")->asModel(User::class)
                ->keyCopy("url")->nullable()
                ->keyCopy("submitted_at")->asDateTime()->nullable()
                ->keyCopy("section")->asModel(Section::class)->nullable());
    }

    /**
     * @param Models\Assignment $assignment
     * @param ?UserProvider $userProvider If provided, will also fetch the users associated with these submissions and pass them to the emitted in the user provider.
     * @return Submission[]
     */
    function getSubmissionsInAssignment(Assignment $assignment, ?UserProviderInterface $userProvider = null) : array{
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
     * @param Models\Submission $submission
     * @return Models\Submission
     */
    public function populateSubmission(Submission $submission): Submission{
        $this->Get("/courses/{$submission->course->id}/assignments/{$submission->assignment->id}/submissions/{$submission->user->id}",
        $submission->getContext(), $this->modelPopulator->withInstance($submission));
        return $submission;
    }
}