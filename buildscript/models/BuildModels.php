<?php
namespace Buildscript\Models;

use function Buildscript\tryExtractModelClassName;

include_once __DIR__ . '/ModelOriginalParser.php';
function buildModels($folder, $targetFolderForTraits){
    $phpFiles = [];
    foreach (glob($folder . '/*.php') as $filePath) {
        $phpFiles[] = [$filePath];
    }
    $mapped = [];
    foreach ($phpFiles as $file) {
        if(str_ends_with($file[0], "Domain.php")){
            continue;//Domain.php is special, it does not have a trait
        }
        $filePath = $file[0];
        $modelname = substr(basename($filePath), 0, -4);
        $traitname = $modelname . "Properties";
        $parsedFile = processModelFile($filePath, $modelname, $traitname);
        [$normalProps, $normalModels] = processAndGetModelClassProps($parsedFile['fields']);
        [$minimumProps, $minimumModels] = processAndGetModelClassProps($parsedFile['minimumProperties']);
        [$nullableProps, $nullableModels] = processAndGetModelClassProps($parsedFile['fieldsNullable']);
        $normalProps = fixGlobalClassTypes($normalProps);
        $nullableProps = fixGlobalClassTypes($nullableProps);
        $minimumProps = fixGlobalClassTypes($minimumProps);
        $parsedFile['generatedTrait'] = GenerateFullModelTrait(
            $modelname, 
            $traitname, 
            $normalProps, 
            $nullableProps, 
            $minimumProps,
            $minimumModels,
            $normalModels, 
            $nullableModels);
        $mapped[] = $parsedFile;
        file_put_contents("$targetFolderForTraits/$traitname.php", $parsedFile["generatedTrait"]);
        //write to test folder
        // $ast = $parsedFile["ast"];
        // $name = $parsedFile["modelname"];
        // unset($parsedFile["ast"]);
        // unset($parsedFile["generatedTrait"]);
        // file_put_contents(__DIR__ . "/../test/" . $name, json_encode($parsedFile, JSON_PRETTY_PRINT) . (new NodeDumper)->dump($ast));
    }
    return  $mapped;
}

function fixGlobalClassTypes($properties){
    $props = [];
    foreach($properties as $property){
        if($property["classType"]){
            $property['type'] = "\\" . $property['type'];
        }
        unset($property["classType"]);
        $props[] = $property;
    }
    return $props;
}

function processAndGetModelClassProps($properties){
    $normalProps = [];
    $modelProps = [];
    foreach($properties as $prop){
        // match \models\Something, models\Something, \models\Something.php, models\Something.php, or arrays like \models\Something[]
        $match = tryExtractModelClassName($prop["type"]);
        if ($match !== null) {
            $prop["type"] = $match;
            $modelProps[] = $prop;
        }
        else{
            $normalProps[] = $prop;
        }
    }
    return [$normalProps, $modelProps];
}