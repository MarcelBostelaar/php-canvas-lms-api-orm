<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Core\Models\Generated;

use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Models\Section;
use CanvasApiLibrary\Core\Models\Submission;

trait SubmissionProperties{
    public ?string $url{
        get {
            return $this->url;
        }
        set(?string $value) {
            $this->url = $value;
        }
    }

    public ?\DateTime $submitted_at{
        get {
            return $this->submitted_at;
        }
        set(?\DateTime $value) {
            $this->submitted_at = $value;
        }
    }

    protected mixed $section_identity;
    public ?Section $section{
        get {
            if($this->section_identity === null){
                return null;
            }
            $item = new Section();
            $item->newFromMinimumDataRepresentation($this->section_identity);
            return $item;
        }
        set (?Section $value) {
            if($value === null){
                $this->section_identity = null;
                return;
            }
            if($value->domain != $this->domain){
                $selfDomain = $this->domain->domain;
                $otherDomain = $value->domain->domain;
                throw new MixingDomainsException("Tried to save a Section from domain '$otherDomain' to Submission.section from domain '$selfDomain'.");
            }
            $this->section_identity = $value->getMinimumDataRepresentation();
        }
    }

}