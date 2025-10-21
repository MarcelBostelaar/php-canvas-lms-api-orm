<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Student;
use CanvasApiLibrary\Models\Assignment;

trait SubmissionProperties{
    abstract protected function getDomain(): Domain;

    public ?string $url{
        get {
            return $this->url;
        }
        set(?string $value) {
            $this->url = $value;
        }
    }

    public ?\DateTime $submittedAt{
        get {
            return $this->submittedAt;
        }
        set(?\DateTime $value) {
            $this->submittedAt = $value;
        }
    }

    protected int $student_id;
    public Student $student{
        get { 
            $item = new Student($this->getDomain());
            $item->id = $this->student_id;
            return $item;
        }
        set (Student $value) {
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a Student from domain '$otherDomain' to Submission.student from domain '$selfDomain'.");
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

}