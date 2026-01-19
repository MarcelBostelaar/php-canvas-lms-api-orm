<?php
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Core\Models\Generated;

use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Core\Models\Outcome;

trait OutcomeProperties{
    public string $title{
        get {
            return $this->title;
        }
        set(string $value) {
            $this->title = $value;
        }
    }

    public string $description{
        get {
            return $this->description;
        }
        set(string $value) {
            $this->description = $value;
        }
    }

    public int $points_possible{
        get {
            return $this->points_possible;
        }
        set(int $value) {
            $this->points_possible = $value;
        }
    }

    public int $mastery_points{
        get {
            return $this->mastery_points;
        }
        set(int $value) {
            $this->mastery_points = $value;
        }
    }

    public string $calculation_method{
        get {
            return $this->calculation_method;
        }
        set(string $value) {
            $this->calculation_method = $value;
        }
    }

    public ?int $calculation_int{
        get {
            return $this->calculation_int;
        }
        set(?int $value) {
            $this->calculation_int = $value;
        }
    }

}