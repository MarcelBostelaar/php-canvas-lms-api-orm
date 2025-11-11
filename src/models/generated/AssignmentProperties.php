<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\GroupCategory;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Assignment;

trait AssignmentProperties{
    public abstract Domain $domain{
        get;
        protected set(Domain $value);
    }
    
    protected mixed $group_category_identity;
    public GroupCategory $group_category{
        get { 
            $item = new GroupCategory();
            $item->newFromMinimumDataRepresentation($this->group_category_identity);
            return $item;
        }
        set (GroupCategory $value) {
            if($value->domain != $this->domain){
                $selfDomain = $this->domain->domain;
                $otherDomain = $value->domain->domain;
                throw new MixingDomainsException("Tried to save a GroupCategory from domain '$otherDomain' to Assignment.group_category from domain '$selfDomain'.");
            }
            $this->group_category_identity = $value->getMinimumDataRepresentation();
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
                throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to Assignment.course from domain '$selfDomain'.");
            }
            $this->course_identity = $value->getMinimumDataRepresentation();
        }
    }

    abstract public function getMinimumDataRepresentation();
    abstract public static function newFromMinimumDataRepresentation(mixed $data): Assignment;
    }