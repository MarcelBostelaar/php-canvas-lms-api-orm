<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;

use CanvasApiLibrary\Core\Exceptions\ChangingIdException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\Course;

/**
 * Add this to models that can optionally be retrieved through a course, among other options,
 * but which does not intrinsically have a course as part of its identity.
 */
trait OptionalCourseContextTrait{

    protected abstract function setMetadata(string $key, $value);
    protected abstract function getMetadata(string $key) : mixed;

    /**
     * Set this value if you are using this model in the context of course-level operations.
     * Doing this allows the api tool and optional caching layers to work more effectively.
     * May be required if the API key does not have certain admin permissions, 
     * in which case the code will throw an exception.
     * Is set automatically if the item has been retrieved from a service using another item bound to a course context directly.
     */
    public ?Course $optionalCourseContext{
        get { 
            $data = $this->getMetadata("optionalcoursecontext");
            if($data === null){
                return null;
            }
            return Course::newFromMinimumDataRepresentation($data, $this->getContext());
        }
        set (Course $value) {
            $data = $this->getMetadata("optionalcoursecontext");
            if(!isset($this->course_identity)){
                if($this->domain != $value->domain){
                    $selfDomain = $this->domain->domain;
                    $otherDomain = $value->domain->domain;
                    throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to Section.course from domain '$selfDomain'.");
                }
                //same domain, allowed to save
                $this->setMetadata("optionalcoursecontext", $value->getMinimumDataRepresentation());            }
            else{
                if($data != $value->getMinimumDataRepresentation()){
                    throw new ChangingIdException("Tried to change the course of this item");
                }
                //Same course, pass.
            }
        }
    }

    protected function initializeOptionalCourseContext(): void {
        $this->contextProcessors[] = function($item) {
            if($item instanceof Course){
                $this->optionalCourseContext = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this->optionalCourseContext];
    }
}