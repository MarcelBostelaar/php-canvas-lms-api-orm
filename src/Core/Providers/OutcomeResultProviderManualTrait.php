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

trait OutcomeResultProviderManualTrait{
    /**
     * Returns the same result of getOutcomeResultsInCourse, but grouped by student
     * @param CourseStub $course
     * @param array $users
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<UserStub,OutcomeResult>>|UnauthorizedResult
     */
    public function getOutcomeResultsGroupedInCourse(CourseStub $course, array $users = [], bool $skipCache = false, bool $doNotCache = false): ErrorResult|NotFoundResult|SuccessResult|UnauthorizedResult {
        $outcomes = $this->getOutcomeResultsInCourse($course, $users, $skipCache, $doNotCache);
        if(!$outcomes instanceof SuccessResult){
            /** @var ErrorResult|NotFoundResult|UnauthorizedResult $outcomes */
            return $outcomes;
        }

        //group per user
        $data = $outcomes->value;
        $lookup = new Lookup();
        $lookup->addMany(fn(OutcomeResult $x) => $x->user, $data);
        // @phpstan-ignore-next-line
        return new SuccessResult($lookup);
    }

    /**
     * Gets all outcome results for several courses, grouped per course, per user.
     * Applies same user filter to all course searches.
     * @param CourseStub[] $courses
     * @param UserStub[] $users
     * @param bool $skipCache
     * @param bool $doNotCache
     * @return ErrorResult|NotFoundResult|SuccessResult<Lookup<CourseStub, Lookup<UserStub, OutcomeResult>>>|UnauthorizedResult
     */
    public function getOutcomeResultsInCoursesFullyGrouped(array $courses, array $users, bool $skipCache, bool $doNotCache){
        $lookup = new Lookup();
        foreach($courses as $course){
            $result = $this->getOutcomeResultsGroupedInCourse($course, $users, $skipCache, $doNotCache);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $lookup->add($course, $result->value);
        }
        
        // @phpstan-ignore-next-line
        return SuccessResult($lookup);
    }
}