<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\User;
use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Section;

trait SubmissionProperties{
    abstract public function getDomain(): Domain;

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

    protected int $student_id;
    public User $student{
        get { 
            $item = new User($this->getDomain());
            $item->id = $this->student_id;
            return $item;
        }
        set (User $value) {
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a User from domain '$otherDomain' to Submission.student from domain '$selfDomain'.");
            }
            $this->student_id = $value->id;
        }
    }

    protected int $assignment_id;
    public Assignment $assignment{
        get { 
            $item = new Assignment($this->getDomain());
            $item->id = $this->assignment_id;
            return $item;
        }
        set (Assignment $value) {
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a Assignment from domain '$otherDomain' to Submission.assignment from domain '$selfDomain'.");
            }
            $this->assignment_id = $value->id;
        }
    }

    protected ?int $course_id;
    public ?Course $course{
        get {
            if($this->course === null){
                return null;
            }
            $item = new Course($this->getDomain());
            $item->id = $this->course_id;
            return $item;
        }
        set (?Course $value) {
            if($value === null){
                $this->course_id = null;
                return;
            }
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to Submission.course from domain '$selfDomain'.");
            }
            $this->course_id = $value->id;
        }
    }

    protected ?int $section_id;
    public ?Section $section{
        get {
            if($this->section === null){
                return null;
            }
            $item = new Section($this->getDomain());
            $item->id = $this->section_id;
            return $item;
        }
        set (?Section $value) {
            if($value === null){
                $this->section_id = null;
                return;
            }
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a Section from domain '$otherDomain' to Submission.section from domain '$selfDomain'.");
            }
            $this->section_id = $value->id;
        }
    }

}