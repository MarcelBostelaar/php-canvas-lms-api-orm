<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\User;
use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Section;
use CanvasApiLibrary\Models\Submission;

trait SubmissionProperties{
    public abstract Domain $domain{
        get;
        protected set(Domain $value);
    }
    
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

    protected mixed $user_identity;
    public User $user{
        get { 
            $item = new User();
            $item->newFromMinimumDataRepresentation($this->user_identity);
            return $item;
        }
        set (User $value) {
            if($value->domain != $this->domain){
                $selfDomain = $this->domain->domain;
                $otherDomain = $value->domain->domain;
                throw new MixingDomainsException("Tried to save a User from domain '$otherDomain' to Submission.user from domain '$selfDomain'.");
            }
            $this->user_identity = $value->getMinimumDataRepresentation();
        }
    }

    protected mixed $assignment_identity;
    public Assignment $assignment{
        get { 
            $item = new Assignment();
            $item->newFromMinimumDataRepresentation($this->assignment_identity);
            return $item;
        }
        set (Assignment $value) {
            if($value->domain != $this->domain){
                $selfDomain = $this->domain->domain;
                $otherDomain = $value->domain->domain;
                throw new MixingDomainsException("Tried to save a Assignment from domain '$otherDomain' to Submission.assignment from domain '$selfDomain'.");
            }
            $this->assignment_identity = $value->getMinimumDataRepresentation();
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
                throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to Submission.course from domain '$selfDomain'.");
            }
            $this->course_identity = $value->getMinimumDataRepresentation();
        }
    }

    protected ?mixed $section_identity;
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

    abstract public function getMinimumDataRepresentation();
    abstract public static function newFromMinimumDataRepresentation(mixed $data): Submission;
    }