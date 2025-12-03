<?php

namespace CanvasApiLibrary\Models\IdentityTraits\Atomic;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Course;

trait CourseIdentityTrait{
    protected mixed $course_identity;
    public Course $course{
        get { 
            return Course::newFromMinimumDataRepresentation($this->course_identity);
        }
        set (Course $value) {
            if(!isset($this->course_identity)){
                if($this->domain != $value->domain){
                    $selfDomain = $this->domain->domain;
                    $otherDomain = $value->domain->domain;
                    throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to Section.course from domain '$selfDomain'.");
                }
                //same domain, allowed to save
                $this->course_identity = $value->getMinimumDataRepresentation();
            }
            else{
                if($this->course_identity != $value->getMinimumDataRepresentation()){
                    throw new ChangingIdException("Tried to change the course of this item");
                }
                //Same course, pass.
            }
        }
    }

    protected function initializeCourseIdentity(): void {
        $this->contextProcessors[] = function($item) {
            if($item instanceof Course){
                $this->course = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this->course];

        $this->mdrGetters[] = fn() => [Course::class => $this->course->id];

        $this->mdrSetters[] = function(&$item, $data) {
            $item->course = Course::newFromMinimumDataRepresentation($data);
        };

        $this->integrityValidators[] = fn() => isset($this->course);

        $this->uniqueIdParts[] = fn() => "Course:" . $this->course->id;
    }
}
