<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Section;
use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;
use function CanvasApiLibrary\Providers\Utility\array_map_to_models;

/**
 * Provider for Canvas API section operations
 * 
 * @method Lookup<Models\Course, Models\Section> getAllSectionsInCourses() Virtual method to get all sections in a course
 */
class SectionProvider extends AbstractProvider{
    public function __construct(public readonly Services\StatusHandlerInterface $statusHandler){}

    /**
     * Summary of getAllGroupsInGroupCategory
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @param \CanvasApiLibrary\Models\Course $course
     * @return Models\Section[]
     */
    public function getAllSectionsInCourse(Domain $domain, Course $course) : array{
        return $this->Get($domain, "/courses/$course->id/sections");
    }

    public function MapData(mixed $data, Domain $domain, array $suplementaryDataMapping = []): array{
        return array_map_to_models($data, $domain, Section::class, [
            "name", 
            ["course_id", fn($v) => new Models\Course($domain, $v)],
            ...$suplementaryDataMapping]);
    }
}
