<?php

namespace Src\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;

trait SectionProperties{
    abstract protected function getDomain(): Domain;

    public ?string $nullableprop{
        get {
            return $this->nullableprop;
        }
        set(?string $value) {
            $this->nullableprop = $value;
        }
    }

    public string $notnullableprop{
        get {
            return $this->notnullableprop;
        }
        set(string $value) {
            $this->notnullableprop = $value;
        }
    }

    protected int $courseId;
    public Course $notnullablemodelprop{
        get { 
            return new Course($this->getDomain(), $this->courseId);
        }
        set (Course $value) {
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to <ModelName>.<Prop name> from domain '$selfDomain'.");
            }
            $this->courseId = $value->id;
        }
    }
    protected ?int $courseId2;
    public ?Course $nullablemodelprop{
        get {
            return $this->courseId2 ? new Course($this->getDomain(), $this->courseId) : null;
        }
        set (?Course $value) {
            if($value === null){
                $this->courseId2 = null;
                return;
            }
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to <ModelName>.<Prop name> from domain '$selfDomain'.");
            }
            $this->courseId = $value->id;
        }
    }
}