<?php
/* Automatically generated based on model properties.*/
namespace Src\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;

trait SubmissionFeedbackProperties{
    abstract protected function getDomain(): Domain;

    public string $feedbackGiver{
        get {
            return $this->feedbackGiver;
        }
        set(string $value) {
            $this->feedbackGiver = $value;
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