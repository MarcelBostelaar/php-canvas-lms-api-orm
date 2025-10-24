<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\GroupCategory;
use CanvasApiLibrary\Models\Course;

trait AssignmentProperties{
    abstract public function getDomain(): Domain;

    protected int $group_category_id;
    public GroupCategory $group_category{
        get { 
            $item = new GroupCategory($this->getDomain());
            $item->id = $this->group_category_id;
            return $item;
        }
        set (GroupCategory $value) {
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a GroupCategory from domain '$otherDomain' to Assignment.group_category from domain '$selfDomain'.");
            }
            $this->group_category_id = $value->id;
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
                throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to Assignment.course from domain '$selfDomain'.");
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

    public static function newFromMinimumDataRepresentation(Domain $domain, array $data): Assignment{
        if(!(            isset($data['id']) &&
                        true
        )){
            throw new NotPopulatedException("Not all minimum required fields for this model are in the data provided.");
        }
        $newInstance = new Assignment($domain);
                $this->id = $data['id'];
        
                return $newInstance;
    }
    }