<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Core\Providers\Generated\Traits;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Models\Domain;

trait CourseProviderProperties{
    
    

    abstract public function getAllCoursesInDomain(Domain $domain) : array;
    
    /**
     * Summary of getAllCoursesInDomains
     * @param Domain[] $domains
     * @return Lookup<Domain, Domain>
     */
    public function getAllCoursesInDomains(array $domains): Lookup{
        $lookup = new Lookup();
        foreach($domains as $domain){
            $lookup->add($domain, $this->getAllCoursesInDomain($domain));
        }
        return $lookup;
    }
}
