<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\GroupCategory;

trait AssignmentProperties{
    abstract protected function getDomain(): Domain;

    protected int $group_id;
    public GroupCategory $group{
        get { 
            $item = new GroupCategory($this->getDomain());
            $item->id = $this->group_id;
            return $item;
        }
        set (GroupCategory $value) {
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a GroupCategory from domain '$otherDomain' to Assignment.group from domain '$selfDomain'.");
            }
            $this->group_id = $value->id;
        }
    }

}