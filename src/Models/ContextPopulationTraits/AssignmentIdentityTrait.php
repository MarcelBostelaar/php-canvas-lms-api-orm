<?php

namespace CanvasApiLibrary\Models\ContextPopulationTraits;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Models\Assignment;

trait AssignmentIdentityTrait{

    use CourseIdentityTrait {
        CourseIdentityTrait::populateWithContext as private cit_populateWithContext;
        CourseIdentityTrait::getContext as private cit_getContext;
        CourseIdentityTrait::getMinimumDataRepresentation as private cit_getMinimumDataRepresentation;
        CourseIdentityTrait::newFromMinimumDataRepresentation as private cit_newFromMinimumDataRepresentation;
        CourseIdentityTrait::validateIdentityIntegrity as private cit_validateIdentityIntegrity;
        CourseIdentityTrait::getUniqueId as private cit_getUniqueId;
    }

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

    /**
     * Populates the model using the provided other models, filling in missing data.
     * @param ModelInterface[] $context A list of context items from which to pull the needed data to populate.
     * @return void
     * @throws ChangingIdException When already set context data is provided again
     * @throws NotPopulatedException When not all required fields are set
     */
    public function populateWithContext(array $context){
        $this->cit_populateWithContext($context);
        foreach($context as $item){
            if($item instanceof Assignment){
                $this->assignment = $item;
                continue;
            }
        }
    }

    public function getContext(): array{
        return [...$this->cit_getContext(), $this->assignment];
    }

    public function getMinimumDataRepresentation(): mixed{
        return [
            ...$this->cit_getMinimumDataRepresentation(),
            Assignment::class => $this->assignment->id
        ];
    }

    public static function newFromMinimumDataRepresentation($data): static{
        $item = static::cit_newFromMinimumDataRepresentation($data);
        $item->assignment = Assignment::newFromMinimumDataRepresentation($data);
        return $item;
    }
    
    public function validateIdentityIntegrity() : bool{
        return $this->cit_validateIdentityIntegrity() && isset($this->assignment);
    }

    public function getUniqueId(): string{
        return $this->cit_getUniqueId() . "-Assignment:" . $this->assignment->id;
    }
}
