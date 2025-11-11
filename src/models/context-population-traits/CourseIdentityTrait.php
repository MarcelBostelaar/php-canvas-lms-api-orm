<?php

namespace CanvasApiLibrary\Models\ContextPopulationTraits;
use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;

trait CourseIdentityTrait{
    abstract public Domain $domain{
        protected set(Domain $value);
        get;
    }

    abstract public int $id{
        get;
        set;
    }

    abstract public Course $course{
        get;
        set;
    }
    
    /**
     * Populates the model using the provided other models, filling in missing data.
     * @param ModelInterface[] $context A list of context items from which to pull the needed data to populate.
     * @return void
     * @throws ChangingIdException When already set context data is provided again
     * @throws NotPopulatedException When not all required fields are set
     */
    public function populateWithContext(array $context){
        foreach($context as $item){
            if($item instanceof Domain){
                if(isset($this->domain)){
                    if($this->domain != $item){
                        throw new ChangingIdException("Tried to set the domain of a model that already exists.");
                    }
                    //same domain
                }
                $this->domain = $item;
                continue;
            }
            if($item instanceof Course){
                if(isset($this->course)){
                    if($this->course != $item){
                        throw new ChangingIdException("Tried to set the course of a model that already exists.");
                    }
                    //same course
                }
                $this->course = $item;
                continue;
            }
        }
    }

    public function getContext(): array{
        return [$this, $this->domain, $this->course];
    }

    public function getMinimumDataRepresentation(): mixed{
        return [
            self::class => $this->id,
            Domain::class => $this->domain->domain,
            Course::class => $this->course->id
        ];
    }

    public static function newFromMinimumDataRepresentation($data){
        $item = new (self::class)();
        $item->id = $data[self::class];
        $item->domain = new Domain($data[Domain::class]);
        $item->course = Course::newFromMinimumDataRepresentation($data);
        return $item;
    }

    public function validateIdentityIntegrity() : bool{
        return isset($this->id) && isset($this->domain) && isset($this->course);
    }
}
