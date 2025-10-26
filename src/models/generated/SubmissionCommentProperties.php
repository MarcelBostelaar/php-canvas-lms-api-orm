<?php

/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\User;
use CanvasApiLibrary\Models\SubmissionComment;
trait SubmissionCommentProperties
{
    abstract public function getDomain(): Domain;
    public string $feedback_giver {
        get {
            return $this->feedback_giver;
        }
        set(string $value) {
            $this->feedback_giver = $value;
        }
    }
    public string $comment {
        get {
            return $this->comment;
        }
        set(string $value) {
            $this->comment = $value;
        }
    }
    public \DateTime $date {
        get {
            return $this->date;
        }
        set(\DateTime $value) {
            $this->date = $value;
        }
    }
    protected int $course_id;
    public Course $course {
        get {
            $item = new Course($this->getDomain());
            $item->id = $this->course_id;
            return $item;
        }
        set(Course $value) {
            if ($value->getDomain()->domain != $this->getDomain()->domain) {
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a Course from domain '{$otherDomain}' to SubmissionComment.course from domain '{$selfDomain}'.");
            }
            $this->course_id = $value->id;
        }
    }
    protected int $assignment_id;
    public Assignment $assignment {
        get {
            $item = new Assignment($this->getDomain());
            $item->id = $this->assignment_id;
            return $item;
        }
        set(Assignment $value) {
            if ($value->getDomain()->domain != $this->getDomain()->domain) {
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a Assignment from domain '{$otherDomain}' to SubmissionComment.assignment from domain '{$selfDomain}'.");
            }
            $this->assignment_id = $value->id;
        }
    }
    protected int $user_id;
    public User $user {
        get {
            $item = new User($this->getDomain());
            $item->id = $this->user_id;
            return $item;
        }
        set(User $value) {
            if ($value->getDomain()->domain != $this->getDomain()->domain) {
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a User from domain '{$otherDomain}' to SubmissionComment.user from domain '{$selfDomain}'.");
            }
            $this->user_id = $value->id;
        }
    }
    public function getMinimumDataRepresentation()
    {
        if (!(isset($this->id) && true)) {
            throw new NotPopulatedException("Not all minimum required fields for this model, so it can be re-populated, have been set.");
        }
        return [['id'] => $this->id];
    }
    public static function newFromMinimumDataRepresentation(Domain $domain, array $data): SubmissionComment
    {
        if (!(isset($data['id']) && true)) {
            throw new NotPopulatedException("Not all minimum required fields for this model are in the data provided.");
        }
        $newInstance = new SubmissionComment($domain);
        $newInstance->id = $data['id'];
        return $newInstance;
    }
}