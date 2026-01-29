<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\CourseStub;

trait CourseIdentityTrait{
    protected mixed $course_identity;
    public CourseStub $course{
        get { 
            return CourseStub::newFromMinimumDataRepresentation($this->course_identity, []); //Course does not require additional info beyond its own minimum data representation. Getting context regularly causes infinite recursion.
        }
        set (CourseStub $value) {
            if(!isset($this->course_identity)){
                if($this->domain != $value->domain){
                    $selfDomain = $this->domain->domain;
                    $otherDomain = $value->domain->domain;
                    throw new MixingDomainsException("Tried to save a CourseStub from domain '$otherDomain' to Section.course from domain '$selfDomain'.");
                }
                //same domain, allowed to save
                $this->course_identity = $value->getMinimumDataRepresentation();
            }
            else{
                if($this->course_identity != $value->getMinimumDataRepresentation()){
                    throw new ChangingIdException("Tried to change the coursestub of this item");
                }
                //Same course, pass.
            }
        }
    }

    protected function initializeCourseIdentity(): void {
        $this->contextProcessors[] = function($item) {
            if($item instanceof CourseStub){
                $this->course = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this->course];

        $this->mdrGetters[] = fn() => [CourseStub::class => $this->course->id];

        $this->mdrSetters[] = function(&$item, $data) {
            $item->course = CourseStub::newFromMinimumDataRepresentation($data, $this->getContext());
        };

        $this->integrityValidators[] = fn() => isset($this->course);

        $this->resourceKeyParts[] = fn() => "CourseStub:" . $this->course->id;
    }
}
