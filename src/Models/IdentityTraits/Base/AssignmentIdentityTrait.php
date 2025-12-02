<?php

namespace CanvasApiLibrary\Models\IdentityTraits\Base;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Models\Assignment;

trait AssignmentIdentityTrait{

    use CourseIdentityTrait;

    protected mixed $assignment_identity;
    public Assignment $assignment{
        get { 
            return Assignment::newFromMinimumDataRepresentation($this->assignment_identity);
        }
        set (Assignment $value) {
            if(!isset($this->assignment_identity)){
                if($this->course_identity != $value->course->getMinimumDataRepresentation()){
                    $selfCourse = implode($this->course_identity);
                    $otherCourse = implode($value->course->getMinimumDataRepresentation());
                    throw new MixingDomainsException("Tried to save an assignment from '$otherCourse' to an item from '$selfCourse'.");
                }
                //same course, allowed to save
                $this->assignment_identity = $value->getMinimumDataRepresentation();
            }
            else{
                if($this->assignment_identity != $value->getMinimumDataRepresentation()){
                    throw new ChangingIdException("Tried to change the assignment of this item");
                }
                //Same assignment, pass.
            }
        }
    }

    protected function initializeAssignmentIdentity(): void {
        $this->contextProcessors[] = function($item) {
            if($item instanceof Assignment){
                $this->assignment = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this->assignment];

        $this->mdrGetters[] = fn() => [Assignment::class => $this->assignment->id];

        $this->mdrSetters[] = function(&$item, $data) {
            $item->assignment = Assignment::newFromMinimumDataRepresentation($data);
        };

        $this->integrityValidators[] = fn() => isset($this->assignment);

        $this->uniqueIdParts[] = fn() => "Assignment:" . $this->assignment->id;
    }
}
