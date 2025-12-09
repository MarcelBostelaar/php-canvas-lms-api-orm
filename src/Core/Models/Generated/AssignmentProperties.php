<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Core\Models\Generated;

use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\GroupCategory;
use CanvasApiLibrary\Core\Models\Assignment;

trait AssignmentProperties{
    protected mixed $group_category_identity;
    public GroupCategory $group_category{
        get { 
            return GroupCategory::newFromMinimumDataRepresentation($this->group_category_identity, $this->getContext());
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

}