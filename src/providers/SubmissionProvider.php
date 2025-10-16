<?php

namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Submission;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\StudentProvider;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;
use function CanvasApiLibrary\Providers\Utility\array_map_to_models;


/**
 * Provider for Canvas API group operations
 * 
 * @method Lookup<Models\Assignment, Models\Submission> getSubmissionsForAssignments() Virtual method to get all groups in group categories
 */
class SubmissionProvider extends AbstractProvider{

    /**
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @param \CanvasApiLibrary\Models\Course $course
     * @param \CanvasApiLibrary\Models\Assignment $assignment
     * @param mixed $studentProvider Optional, if provided will be used to pre-fetch student info and emit to student provider
     * @return Submission[]
     */
    protected function getSubmissionsForAssignment(Domain $domain, Course $course, Assignment $assignment, ?StudentProvider $studentProvider = null) : array{
        $postfix = $studentProvider ? "?include[]=user" : "";
        return $this->Get($domain, "courses/$course->id/assignments/$assignment->id/submissions$postfix",
            $studentProvider ? [["user", $studentProvider]] : []
        );
    }


    public function MapData(mixed $data, Domain $domain, array $suplementaryDataMapping = []): array{
        return array_map_to_models($data, $domain, Submission::class, [
            "url", 
            ["submittedAt", fn($v) => $v ? new \DateTime($v) : null],
            ["assignment", "assignment_id", fn($v) => new Models\Assignment($domain, $v)],
            ["student", "user_id", fn($v) => new Models\Student($domain, $v)],
            ...$suplementaryDataMapping
        ]);
    }
}