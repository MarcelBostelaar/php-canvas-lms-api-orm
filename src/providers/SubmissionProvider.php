<?php

namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Section;
use CanvasApiLibrary\Models\Submission;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\UserProvider;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;



/**
 * Provider for Canvas API group operations
 * 
 * @method Lookup<Models\Assignment, Models\Submission> getSubmissionsForAssignments() Virtual method to get all groups in group categories
 */
class SubmissionProvider extends AbstractProvider{
    use SubmissionProviderProperties;

    protected static $modelPopulator =
    new ModelPopulationConfigBuilder(Submission::class)
    ->from("user_id")->to("user")->asModel(User::class)
    ->keyCopy("url")->nullable()
    ->keyCopy("submitted_at")->asDateTime()->nullable()
    ->keyCopy("section")->asModel(Section::class)->nullable();

    /**
     * @param Models\Assignment $assignment
     * @param ?UserProvider $userProvider If provided, will also fetch the users associated with these submissions and pass them to the emitted in the user provider.
     * @return Submission[]
     */
    function getSubmissionsForAssignment(Assignment $assignment, ?UserProvider $userProvider = null) : array{
        $postfix = "";
        $builder = self::$modelPopulator;
        if($userProvider !== null){
            $postfix = "?include[]=user";
            $builder = $builder->from("user")->emittingConsumer($userProvider);
        }
        $courseID = $assignment->course->id;
        return $this->GetMany("courses/$courseID/assignments/$assignment->id/submissions$postfix", $assignment->getContext(),
            $builder
        );
    }

    public function populateSubmission(Submission $submission): Submission{
        $this->Get("/courses/{$submission->course->id}/assignments/{$submission->assignment->id}/submissions/{$submission->user->id}",
        $submission->getContext(), self::$modelPopulator->withInstance($submission));
        return $submission;
    }
}