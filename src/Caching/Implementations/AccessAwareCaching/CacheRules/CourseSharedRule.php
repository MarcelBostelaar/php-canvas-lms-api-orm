<?php

namespace CanvasApiLibrary\Caching\Implementations\AccessAwareCaching\CacheRules;

use CanvasApiLibrary\Caching\Utility\CacheRule;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use Exception;

class CourseSharedRule extends CacheRule{
    public function processArgs(mixed ...$args): mixed{
        foreach($args as $arg){
            if($arg instanceof ModelInterface){
                if(property_exists($arg, "course") && isset($arg->course) && $arg->course instanceof Course){
                    return $arg->course->getUniqueId();
                }
            }
        }
        throw new Exception("Tried to get the course corresponding to this course-shared cache call, but could not find a course object.");
    }
}