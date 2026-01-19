<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Core\Models\Generated;

use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\OutcomegroupStub;
use CanvasApiLibrary\Core\Models\Outcomegroup;

trait OutcomegroupProperties{
    public string $title{
        get {
            return $this->title;
        }
        set(string $value) {
            $this->title = $value;
        }
    }

    public string $description{
        get {
            return $this->description;
        }
        set(string $value) {
            $this->description = $value;
        }
    }

    protected mixed $parent_outcome_group_identity;
    public ?OutcomegroupStub $parent_outcome_group{
        get {
            if($this->parent_outcome_group_identity === null){
                return null;
            }
            $item = new OutcomegroupStub();
            $item->newFromMinimumDataRepresentation($this->parent_outcome_group_identity, $this->getContext());
            return $item;
        }
        set (?OutcomegroupStub $value) {
            if($value === null){
                $this->parent_outcome_group_identity = null;
                return;
            }
            if($value->domain != $this->domain){
                $selfDomain = $this->domain->domain;
                $otherDomain = $value->domain->domain;
                throw new MixingDomainsException("Tried to save a OutcomegroupStub from domain '$otherDomain' to Outcomegroup.parent_outcome_group from domain '$selfDomain'.");
            }
            $this->parent_outcome_group_identity = $value->getMinimumDataRepresentation();
        }
    }

}