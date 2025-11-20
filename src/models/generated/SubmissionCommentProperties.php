<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\User;
use CanvasApiLibrary\Models\SubmissionComment;

trait SubmissionCommentProperties{
    public string $feedback_giver{
        get {
            return $this->feedback_giver;
        }
        set(string $value) {
            $this->feedback_giver = $value;
        }
    }

    public string $comment{
        get {
            return $this->comment;
        }
        set(string $value) {
            $this->comment = $value;
        }
    }

    public \DateTime $date{
        get {
            return $this->date;
        }
        set(\DateTime $value) {
            $this->date = $value;
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
                throw new MixingDomainsException("Tried to save a Course from domain '$otherDomain' to SubmissionComment.course from domain '$selfDomain'.");
            }
            $this->course_identity = $value->getMinimumDataRepresentation();
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
                throw new MixingDomainsException("Tried to save a Assignment from domain '$otherDomain' to SubmissionComment.assignment from domain '$selfDomain'.");
            }
            $this->assignment_identity = $value->getMinimumDataRepresentation();
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
                throw new MixingDomainsException("Tried to save a User from domain '$otherDomain' to SubmissionComment.user from domain '$selfDomain'.");
            }
            $this->user_identity = $value->getMinimumDataRepresentation();
        }
    }

    abstract public function getMinimumDataRepresentation();
    abstract public static function newFromMinimumDataRepresentation(mixed $data): static;
    }