<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;

trait SectionProperties{
    abstract protected function getDomain(): Domain;

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

}