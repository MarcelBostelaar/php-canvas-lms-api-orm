<?php
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

use CanvasApiLibrary\Core\Models\Domain;

interface CourseProviderInterface extends HandleEmittedInterface{

    public function getClientID(): string;
    /**
    * @param Domain[] $domains
    * @return Lookup<Domain, Domain>
    */
    public function getAllCoursesInDomains(array $domains) : Lookup;

    /**
    * @param Domain $domain
    * @return mixed
    */
    public function getAllCoursesInDomain(Domain $domain) : mixed;

}
