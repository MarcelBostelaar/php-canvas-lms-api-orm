<?php
/* Automatically generated based on model properties on 2025-10-16 21:39:55*/
namespace Src\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Section;
use CanvasApiLibrary\Models\Student;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Submission;

trait TestTrait{
    abstract protected function getDomain(): Domain;

    public string $myNormalString{
        get {
            return $this->myNormalString;
        }
        set(string $value) {
            $this->myNormalString = $value;
        }
    }

    public int $myNormalInt{
        get {
            return $this->myNormalInt;
        }
        set(int $value) {
            $this->myNormalInt = $value;
        }
    }

    public ?string $myNullableString{
        get {
            return $this->myNullableString;
        }
        set(?string $value) {
            $this->myNullableString = $value;
        }
    }

    public ?int $myullableInt{
        get {
            return $this->myullableInt;
        }
        set(?int $value) {
            $this->myullableInt = $value;
        }
    }

    protected int $mySection_id;
    public Section $mySection{
        get { 
            return new Section($this->getDomain(), $this->mySection_id);
        }
        set (Section $value) {
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to Section.mySection from domain '$selfDomain'.");
            }
            $this->mySection_id = $value->id;
        }
    }

    protected int $myStudent_id;
    public Student $myStudent{
        get { 
            return new Student($this->getDomain(), $this->myStudent_id);
        }
        set (Student $value) {
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to Student.myStudent from domain '$selfDomain'.");
            }
            $this->myStudent_id = $value->id;
        }
    }

    protected ?int $myNullableCourse_id;
    public ?Course $myNullableCourse{
        get {
            return $this->myNullableCourse ? new Course($this->getDomain(), $this->myNullableCourse_id) : null;
        }
        set (?Course $value) {
            if($value === null){
                $this->myNullableCourse_id = null;
                return;
            }
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to Course.myNullableCourse from domain '$selfDomain'.");
            }
            $this->myNullableCourse_id = $value->id;
        }
    }

    protected ?int $myNullableSubmission_id;
    public ?Submission $myNullableSubmission{
        get {
            return $this->myNullableSubmission ? new Submission($this->getDomain(), $this->myNullableSubmission_id) : null;
        }
        set (?Submission $value) {
            if($value === null){
                $this->myNullableSubmission_id = null;
                return;
            }
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to Submission.myNullableSubmission from domain '$selfDomain'.");
            }
            $this->myNullableSubmission_id = $value->id;
        }
    }

}