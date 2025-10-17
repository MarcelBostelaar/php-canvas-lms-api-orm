<?php
/* Automatically generated based on model properties.*/
namespace Src\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\GroupCategory;

trait AssignmentProperties{
    abstract protected function getDomain(): Domain;

    protected int $group_id;
    public GroupCategory $group{
        get { 
            return new GroupCategory($this->getDomain(), $this->group_id);
        }
        set (GroupCategory $value) {
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to GroupCategory.group from domain '$selfDomain'.");
            }
            $this->group_id = $value->id;
        }
    }

}