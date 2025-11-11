<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Section;

trait SectionProperties{
    public abstract Domain $domain{
        get;
        protected set(Domain $value);
    }
    
    public string $name{
        get {
            return $this->name;
        }
        set(string $value) {
            $this->name = $value;
        }
    }

    protected mixed $course_identity;
    public Course $course{
        get { 
            $item = new Course();
            $item->newFromMinimumDataRepresentation($this->course_identity);
            return $item;
        }
        set (Course $value) {
            if($value->domain != $this->domain){
                $selfDomain = $this->domain->domain;
                $otherDomain = $value->domain->domain;
                throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to Section.course from domain '$selfDomain'.");
            }
            $this->course_identity = $value->getMinimumDataRepresentation();
        }
    }

    abstract public function getMinimumDataRepresentation();
    abstract public static function newFromMinimumDataRepresentation(mixed $data): Section;
    }