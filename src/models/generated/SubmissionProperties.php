<?php
/* Automatically generated based on model properties.*/
namespace Src\Models\Generated;

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
            return new Student($this->getDomain(), $this->student_id);
        }
        set (Student $value) {
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to Student.student from domain '$selfDomain'.");
            }
            $this->student_id = $value->id;
        }
    }

    protected int $assignment_id;
    public Assignment $assignment{
        get { 
            return new Assignment($this->getDomain(), $this->assignment_id);
        }
        set (Assignment $value) {
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to Assignment.assignment from domain '$selfDomain'.");
            }
            $this->assignment_id = $value->id;
        }
    }

}