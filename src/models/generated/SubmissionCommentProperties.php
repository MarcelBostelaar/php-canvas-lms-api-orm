<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
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

}