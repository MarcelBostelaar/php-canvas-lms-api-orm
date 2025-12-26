<?php

namespace CanvasApiLibrary\Core\Models\IdentityTraits\Atomic;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;
use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\AssignmentStub;

trait AssignmentIdentityTrait{
    protected mixed $assignment_identity;
    public AssignmentStub $assignment{
        get { 
            return AssignmentStub::newFromMinimumDataRepresentation($this->assignment_identity, $this->getContext());
        }
        set (Assignment $value) {
            if(!isset($this->assignment_identity)){
                if($this->course_identity != $value->course->getMinimumDataRepresentation()){
                    $selfCourse = implode($this->course_identity);
                    $otherCourse = implode($value->course->getMinimumDataRepresentation());
                    throw new MixingDomainsException("Tried to save an assignmentStub from '$otherCourse' to an item from '$selfCourse'.");
                }
                //same course, allowed to save
                $this->assignment_identity = $value->getMinimumDataRepresentation();
            }
            else{
                if($this->assignment_identity != $value->getMinimumDataRepresentation()){
                    throw new ChangingIdException("Tried to change the assignmentStub of this item");
                }
                //Same assignment, pass.
            }
        }
    }

    protected function initializeAssignmentIdentity(): void {
        $this->contextProcessors[] = function($item) {
            if($item instanceof AssignmentStub){
                $this->assignment = $item;
                return true;
            }
            return false;
        };

        $this->contextGetters[] = fn() => [$this->assignment];

        $this->mdrGetters[] = fn() => [AssignmentStub::class => $this->assignment->id];

        $this->mdrSetters[] = function(&$item, $data) {
            $item->assignment = AssignmentStub::newFromMinimumDataRepresentation($data, $this->getContext());
        };

        $this->integrityValidators[] = fn() => isset($this->assignment);

        $this->resourceKeyParts[] = fn() => "AssignmentStub:" . $this->assignment->id;
    }
}
