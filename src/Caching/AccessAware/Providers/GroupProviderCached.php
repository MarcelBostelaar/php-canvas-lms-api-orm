<?php

namespace CanvasApiLibrary\Caching\AccessAware\Providers;

use CanvasApiLibrary\Caching\AccessAware\Interfaces\CacheStorage;
use CanvasApiLibrary\Caching\AccessAware\PermissionsHandler;
use CanvasApiLibrary\Core\Providers\GroupProvider;
use CanvasApiLibrary\Core\Providers\Generated\Traits\GroupProviderProperties;
use CanvasApiLibrary\Core\Providers\Interfaces\GroupProviderInterface;

class GroupProviderCached implements GroupProviderInterface{

    use GroupProviderProperties;
    public function __construct(
        private readonly GroupProvider $wrapped,
        private readonly CacheStorage $cache,
        private readonly int $ttl
    ) {
    }

    public function HandleEmitted(mixed $data, array $context){
        return $this->wrapped->HandleEmitted($data, $context);
    }

    public function getClientID(): string{
        return $this->wrapped->getClientID();
    }

    public function getAllGroupsInGroupCategory(\CanvasApiLibrary\Core\Models\GroupCategory $category): array{
        //TODO add permission backpropagation. Een groupcategory en een group hebben niet een indicatie waarbij ze horen, maar:
        // 1. In een group zitten studenten.
        // 2. Groups zitten in een category
        // Zodra van een group users worden opgehaald, kan je die user permissions toepassen op de group
        // Zodra de group een userpermission heeft, kan je die toepaasen op de group category
        // TODO implementeer permission dependency in de cache provider, 
        // dat automatisch permissions backpropagate als deze gevonden worden.
        // Misschien dat backpropagation per soort permission gelimiteerd moet worden?
        //  Of levert het geen probleem op dat de permission van een student deze route aflegt:
        // User (user+course+domain) -> Group -> Group category -> assignment -> course
        // Ik denk dat handmatig de backpropagation relatief definieren genoeg is, want het is niet nodig
        // om assignment de permissions van een group category te geven, 
        // want de assignment hoord al bij een course, een van de 3 soorten permission (user/course/domain, course/domain, individual)
        
        //Cant ensure permissions for a course because GC is not neccecarily course bound.
        $collectionKey = "getAllGroupsInGroupCategory" . $category->getUniqueId();
        $item = $this->cache->getCollection(
            $collectionKey,
            $this->getClientID()
        );
        if($item->hit){
            return $item->value;
        }
        //No hit
        $actualItems = $this->getAllGroupsInGroupCategory($category);
        $exampleContext = unknown;
        $permissionType
        $this->cache->ensureCollection(
            $collectionKey,

        );

    }

    public function populateGroup(\CanvasApiLibrary\Core\Models\Group $group): \CanvasApiLibrary\Core\Models\Group{
        return $this->cache->trySingleValue(
            $group->getUniqueId(),
            $this->ttl,
            $this->getClientID(),
            PermissionsHandler::individualPermission($group->getUniqueId()),
            fn()=> $this->wrapped->populateGroup($group)
        );
    }
}
