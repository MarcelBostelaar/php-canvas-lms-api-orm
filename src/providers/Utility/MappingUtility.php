<?php
namespace CanvasApiLibrary\Providers\Utility;

use CanvasApiLibrary\Models\Domain;

/**
 * Creates new model instances populated with the data given.
 * @param array $data An array of associative arrays, each representing the data for one model instance.
 * @param Domain $domain The domain of the canvas object
 * @param string $modelClass The class name of the model to create instances of.
 * @param array $keymapping An array of key mappings. Each mapping can be either a string (indicating the same key in both source data and model) or an array of two strings (the first being the key in the source data, the second being the property name in the model).
 * @throws \Exception if the key mapping is invalid.
 * @return mixed[] An array of model instances populated with the data. 
 */
function array_map_to_models(array $data, Domain $domain, string $modelClass, array $keymapping){
    $keymapping = processRules($keymapping, $domain);

    $results = [];
    foreach($data as $item){
        $newModel = new $modelClass($domain, $item["id"]);
        foreach($keymapping as $mapping){
            $mapping["process"]($newModel, $item[$mapping["key"]]);
        }
        $results[] = $newModel;
    }
    return $results;
}


function processRules(array $keymappings, Domain $domain){
    $keymappings = array_map(fn($x) => handle_provider($x, $domain), $keymappings);
    $keymappings = array_map(fn($x) => handle_string($x), $keymappings);
    $keymappings = array_map(fn($x) => handle_string_string($x), $keymappings);
    $keymappings = array_map(fn($x) => handle_string_process($x), $keymappings);
    $keymappings = array_map(fn($x) => handle_string_string_process($x), $keymappings);
    foreach($keymappings as $mapping){
        if(!is_array($mapping) || !isset($mapping["key"]) || !isset($mapping["process"])){
            throw new \Exception("Invalid key mapping: " . serialize($mapping));
        }
    }
    return $keymappings;
}

/**
 * Setup processor that takes data from the first source and puts it in the the instance using the same key.
 * @param mixed $x
 */
function handle_string($x){
    if(is_string($x)){
        //Turn into [key, key], to be handled by next step
        return [$x, $x];
    }
    return $x;
}

/**
 * Setup processor that takes data from the first key in source and puts it in the second key in the instance.
 * @param mixed $x
 */
function handle_string_string($x){
    if(is_array($x) && count($x) == 2 && is_string($x[0]) && is_string($x[1])){
        //Turn into [key, key, identity processor], to be handled by next step
        return [$x[0], $x[1], fn($identity) => $identity];
    }
    return $x;
}

function handle_string_process($x){
    if(is_array($x) && count($x) == 2 && is_string($x[0]) && is_callable($x[1])){
        //Turn into [key, key, processor], to be handled by next step
        return [$x[0], $x[0], $x[1]];
    }
    return $x;
}

/**
 * Setup processor that passes data found in key to handleEmitted. Used to parse additional info on call.
 * @param mixed $x
 */
function handle_provider($x, Domain $domain){
    if(is_array($x) && count($x) == 2 && is_string($x[0]) && is_object($x[1])){
        if($x[1] instanceof AbstractProvider){
            return [
                "key" => $x[0],
                "process" => fn($instance, $value) => $x[1]->HandleEmitted($value, $domain)
            ];
        }
    }
    return $x;
}

function handle_string_string_process($x){
    if(is_array($x) && count($x) == 3 && is_string($x[0]) && is_string($x[1]) && is_callable($x[2])){
        return [
            "key" => $x[0],
            "process" => fn($instance, $value) => $instance->{$x[1]} = $x[2]($value)
        ];
    }
    return $x;
}