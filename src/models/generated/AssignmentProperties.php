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
    protected mixed $group_category_identity;
    public GroupCategory $group_category{
        get { 
            return GroupCategory::newFromMinimumDataRepresentation($this->group_category_identity);
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
            return Course::newFromMinimumDataRepresentation($this->course_identity);
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
    abstract public static function newFromMinimumDataRepresentation(mixed $data): static;
    }