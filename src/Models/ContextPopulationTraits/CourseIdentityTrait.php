<?php

namespace CanvasApiLibrary\Models\ContextPopulationTraits;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Course;

trait CourseIdentityTrait{

    use DomainIdentityTrait {
        DomainIdentityTrait::populateWithContext as private dit_populateWithContext;
        DomainIdentityTrait::getContext as private dit_getContext;
        DomainIdentityTrait::getMinimumDataRepresentation as private dit_getMinimumDataRepresentation;
        DomainIdentityTrait::newFromMinimumDataRepresentation as private dit_newFromMinimumDataRepresentation;
        DomainIdentityTrait::validateIdentityIntegrity as private dit_validateIdentityIntegrity;
        DomainIdentityTrait::getUniqueId as private dit_getUniqueId;
    }

    
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

    public function populateWithContext(array $context){
        $this->dit_populateWithContext($context);
        foreach($context as $item){
            if($item instanceof Course){
                $this->course = $item;
                continue;
            }
        }
    }

    public function getContext(): array{
        return [...$this->dit_getContext(), $this->course];
    }

    public function getMinimumDataRepresentation(): mixed{
        return [
            ...$this->dit_getMinimumDataRepresentation(),
            Course::class => $this->course->id
        ];
    }

    public static function newFromMinimumDataRepresentation($data): static{
        $item = static::dit_newFromMinimumDataRepresentation($data);
        $item->course = Course::newFromMinimumDataRepresentation($data);
        return $item;
    }

    public function validateIdentityIntegrity() : bool{
        return $this->dit_validateIdentityIntegrity() && isset($this->course);
    }

    public function getUniqueId(): string{
        return $this->dit_getUniqueId() . "-Course:" . $this->course->id;
    }
}
