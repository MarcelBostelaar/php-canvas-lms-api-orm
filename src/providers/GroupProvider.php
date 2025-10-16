<?php
namespace CanvasApiLibrary\Providers;
use CanvasApiLibrary\Models as Models;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Services as Services;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\Utility\AbstractProvider;
use CanvasApiLibrary\Providers\Utility\Lookup;
use function CanvasApiLibrary\Providers\Utility\array_map_to_models;

/**
 * Provider for Canvas API group operations
 * 
 * @method Lookup<Models\GroupCategory, Models\Group> GetAllGroupsInGroupCategories() Virtual method to get all groups in group categories
 */
class GroupProvider extends AbstractProvider{
    public function __construct(public readonly Services\StatusHandlerInterface $statusHandler){}

    /**
     * Summary of getAllGroupsInGroupCategory
     * @param \CanvasApiLibrary\Models\GroupCategory $category
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @return Models\Group[]
     */
    public function getAllGroupsInGroupCategory(Models\GroupCategory $category, Domain $domain) : array{
        return $this->Get($domain, "/group_categories/{$category->id}/groups");
    }

    public function MapData(mixed $data, Domain $domain, array $suplementaryDataMapping = []): array{
        return array_map_to_models($data, $domain, Group::class, ["name", ...$suplementaryDataMapping]);
    }
}