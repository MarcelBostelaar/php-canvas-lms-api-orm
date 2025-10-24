<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;

trait SectionProperties{
    abstract public function getDomain(): Domain;

    public string $name{
        get {
            return $this->name;
        }
        set(string $value) {
            $this->name = $value;
        }
    }

    protected int $course_id;
    public Course $course{
        get { 
            $item = new Course($this->getDomain());
            $item->id = $this->course_id;
            return $item;
        }
        set (Course $value) {
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to Section.course from domain '$selfDomain'.");
            }
            $this->course_id = $value->id;
        }
    }

    public function getMinimumDataRepresentation(){
        if(!(            isset($this->id) &&
                        true
        )){
            throw new NotPopulatedException("Not all minimum required fields for this model, so it can be re-populated, have been set.");
        }
        return [
            ['id'] => $this->id        ];
    }

    public static function newFromMinimumDataRepresentation(Domain $domain, array $data): Section{
        if(!(            isset($data['id']) &&
                        true
        )){
            throw new NotPopulatedException("Not all minimum required fields for this model are in the data provided.");
        }
        $newInstance = new Section($domain);
                $this->id = $data['id'];
        
                return $newInstance;
    }
    }