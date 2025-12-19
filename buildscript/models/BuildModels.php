<?php
namespace Buildscript\Models;

use Buildscript\ModelParseResult;
use Buildscript\PropertyDefinition;
use function Buildscript\tryExtractModelClassName;
use function Buildscript\prettified;

include_once __DIR__ . '/ModelOriginalParser.php';
/**
 * @return ModelParseResult[]
 */
function buildModels($folder, $targetFolderForTraits): array {
    $phpFiles = [];
    foreach (glob($folder . '/*.php') as $filePath) {
        $phpFiles[] = [$filePath];
    }
    $mapped = [];
    foreach ($phpFiles as $file) {
        // if(str_ends_with($file[0], "Domain.php")){
        //     continue;//Domain.php is special, it does not have a trait
        // }
        $filePath = $file[0];
        $modelname = substr(basename($filePath), 0, -4);
        $traitname = $modelname . "Properties";
        $parsedFile = processModelFile($filePath, $modelname, $traitname);
        [$normalProps, $normalModels] = processAndGetModelClassProps($parsedFile->fields);
        [$nullableProps, $nullableModels] = processAndGetModelClassProps($parsedFile->fieldsNullable);
        $normalProps = fixGlobalClassTypes($normalProps);
        $nullableProps = fixGlobalClassTypes($nullableProps);
        $generatedTrait = GenerateFullModelTrait(
            $modelname, 
            $traitname, 
            $normalProps, 
            $nullableProps,
            $normalModels, 
            $nullableModels);
        $parsedFile = $parsedFile->withGeneratedTrait($generatedTrait);
        $mapped[] = $parsedFile;
        file_put_contents("$targetFolderForTraits/$traitname.php", prettified($generatedTrait));
        //write to test folder
        // $ast = $parsedFile->ast;
        // $name = $parsedFile->modelname;
        // file_put_contents(__DIR__ . "/../test/" . $name, json_encode($parsedFile->toArray(), JSON_PRETTY_PRINT) . (new NodeDumper)->dump($ast));
    }
    return  $mapped;
}

/**
 * @param PropertyDefinition[] $properties
 * @return PropertyDefinition[]
 */
function fixGlobalClassTypes(array $properties): array {
    $props = [];
    foreach($properties as $property){
        if($property->classType){
            $property = new PropertyDefinition(
                "\\" . $property->type,
                $property->name,
                false
            );
        } else {
            $property = new PropertyDefinition(
                $property->type,
                $property->name,
                false
            );
        }
        $props[] = $property;
    }
    return $props;
}

/**
 * @param PropertyDefinition[] $properties
 * @return array{PropertyDefinition[], PropertyDefinition[]}
 */
function processAndGetModelClassProps(array $properties): array {
    $normalProps = [];
    $modelProps = [];
    foreach($properties as $prop){
        // match \models\Something, models\Something, \models\Something.php, models\Something.php, or arrays like \models\Something[]
        $match = tryExtractModelClassName($prop->type);
        if ($match !== null) {
            $prop = new PropertyDefinition($match, $prop->name, $prop->classType);
            $modelProps[] = $prop;
        }
        else{
            $normalProps[] = $prop;
        }
    }
    return [$normalProps, $modelProps];
}