<?php

namespace Buildscript\Providers;
use function Buildscript\tryExtractModelClassName;

/**
 * Genereates the start of the class.
 * @param string $traitname
 * @param string $modelname
 * @param string[] $modelPlurals
 * @param string[] $usedModels
 * @return array{
 *     name: string,
 *     parameters: array{
 *          type: string,
 *          name: string,
 *          annotatedtype: string
 *     },
 *     returnType: array{
 *          type: string,
 *          annotatedtype: string
 *     }
 * } The created method
 */
function fileTop($traitname, $modelname, $modelPlurals, $usedModels){
?>
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Providers\Generated\Traits;

use CanvasApiLibrary\Providers\Utility\Lookup;
<?php
    foreach($usedModels as $usedModel){ ?>
use CanvasApiLibrary\Models\<?=$usedModel?>;
<?php
    }
?>

trait <?=$traitname?>{
    abstract public function populate<?=$modelname?>(<?=$modelname?> $<?=lcfirst($modelname)?>);
<?php
    //Generate a plural variant for each plural name variant.
    foreach($modelPlurals as $pluralName){?>
    
    /**
     * Array variant of populate<?=$modelname?>

     * @param <?=$modelname?>[] $<?=lcfirst($pluralName)?>

     * @return <?=$modelname?>[]
     */
    public function populate<?=$pluralName?>(array $<?=lcfirst($pluralName)?>): array{
        return array_map(fn($x) => $this->populate<?=$modelname?>($x), $<?=lcfirst($pluralName)?>);
    }
<?php
    }
    return [
        "name" => "populate$pluralName",
        "parameters" => [[
            "name" => lcfirst($pluralName),
            "type" => "array",
            "annotatedtype" => $modelname.'[]'
        ]],
        "returnType" => [
            "type" => "array",
            "annotatedtype" => $modelname.'[]'
        ]
    ];
}

/**
 * Generates an abstract method that can be found in the original provider.
 * Example: GetAllCactiForCity & GetAllCactiForCities (former will be generated)
 * @param string $name GetAllCactiForCity
 * @param array{array{
 *  name: string,
 *  type: string
 * }} $params 
 * @return void
 */
function generateAbstractOriginal($name, $params){
    $paramString = implode(', ', array_map(fn($p) => "{$p['type']} \${$p['name']}", $params));
    ?>

    abstract public function <?=$name?>(<?=$paramString?>) : array;
<?php
}

/**
 * Generates an alias function that just calls the original.
 * @param array{methodPrefix: string, originalModelPlural: string, originalSubjectSingular: string, 
 *      parameters: array{
 *          type: string,
 *          name: string
 *     }, returnType: string} $aliasInfo
 * @param array{methodPrefix: string, originalModelPlural: string, originalSubjectSingular: string, 
 *      parameters: array{
 *          type: string,
 *          name: string
 *     }, returnType: string} $original
 * @return array{
 *     name: string,
 *     parameters: array{
 *          type: string,
 *          name: string
 *     },
 *     returnType: array{
 *          type: string,
 *          annotatedtype: string
 *     }
 * }
 */
function generateAlias($aliasInfo, $original){
    $newMethodName = $aliasInfo['methodPrefix'] . $aliasInfo['originalModelPlural'] . 'In' . $aliasInfo['originalSubjectSingular'];
    $originalMethodName = $original['methodPrefix'] . $original['originalModelPlural'] . 'In' . $original['originalSubjectSingular'];
    $paramString = implode(', ', array_map(fn($p) => "{$p['type']} \${$p['name']}", $aliasInfo['parameters']));
    $originalParamString = implode(', ', array_map(fn($p) => "\${$p['name']}", $original['parameters']));
    ?>

    /**
     * Alias of <?=$originalMethodName?>

     */
    public function <?=$newMethodName?>(<?=$paramString?>): <?=$aliasInfo['returnType']?>{
        return $this-><?=$originalMethodName?>(<?=$originalParamString?>);
    }
    <?php
    return [
        "name" => $newMethodName,
        "parameters" => $aliasInfo['parameters'],
        "returnType" => $aliasInfo['returnType']
    ];
}

/**
 * Generates a multi method for a given original.
 * Example: GetAllCactiForCity & GetAllCactiForCities (latter will be generated)
 * @param string $methodPrefix GetAll
 * @param string $originalSubjectName City
 * @param string $subjectPluralName Cities
 * @param string $modelName Cactus
 * @param string $modelPluralName Cacti
 * @param array{array{
 *  name: string,
 *  type: string
 * }} $params Unmodified params of the original. Will be inline replaced.
 * @return array{
 *     name: string,
 *     parameters: array{
 *          type: string,
 *          name: string
 *     },
 *     returnType: array{
 *          type: string,
 *          annotatedtype: string
 *     }
 * }
 */
function generateMultiMethod($methodPrefix, $originalSubjectName, $subjectPluralName, $modelName, $modelPluralName, $params){
    $multiParams = array_map(function($p) use ($originalSubjectName, $subjectPluralName){
        if($p['type'] === $originalSubjectName){
            return [
                'name' => lcfirst($subjectPluralName),
                'type' => 'array',
                'annotatedtype' => $originalSubjectName . "[]",
                'isArrayVariant' => true
            ];
        }
        return [
            'name' => $p['name'],
            'type' => $p['type'],
            'annotatedtype' => $p['type'],
            'isArrayVariant' => false
        ];
    }, $params);
    $multiMethodName = $methodPrefix . $modelPluralName . 'In' . $subjectPluralName;
    $originalMethodName = $methodPrefix . $modelPluralName . 'In' . $originalSubjectName;
    $originalParamString = implode(', ', array_map(fn($p) => $p['isArrayVariant'] ? '$' . lcfirst($originalSubjectName) : "\${$p['name']}", $multiParams));
    $multiParamString = implode(', ', array_map(fn($p) => "{$p['type']} \${$p['name']}", $multiParams));
    ?>
    
    /**
     * Summary of <?=$multiMethodName?>

     * @param <?=$originalSubjectName?>[] $<?=lcfirst($subjectPluralName)?>

     * @return Lookup<<?=$originalSubjectName?>, <?=$modelName?>>
     */
    public function <?=$multiMethodName?>(<?=$multiParamString?>): Lookup{
        $lookup = new Lookup();
        foreach($<?=lcfirst($subjectPluralName)?> as $<?=lcfirst($originalSubjectName)?>){
            $lookup->add($<?=lcfirst($originalSubjectName)?>, $this-><?=$originalMethodName?>(<?=$originalParamString?>));
        }
        return $lookup;
    }
<?php
    return [
        "name" => $multiMethodName,
        "parameters" => $multiParams,
        "returnType" => [
            "type" => "Lookup",
            "annotatedtype" => "Lookup<$originalSubjectName, $modelName>"
        ]
    ];
}

function fileEnd(){
    echo "}\n";
}

/**
 * Creates a structured info item for each method.
 * @param array{
 *     name: string,
 *     parameters: array{
 *          type: string,
 *          name: string
 *     },
 *     returnType: string
 * } $methodsToGenerateFor
 * @param string[] $modelPlurals
 * @return array{methodPrefix: string, originalModelPlural: string, originalSubjectSingular: string, 
 *      parameters: array{
 *          type: string,
 *          name: string
 *     }, returnType: string}
 */
function processMethods($methodsToGenerateFor, $modelPlurals, $modelOriginalName){
    $methodsToGenerateFor = array_filter($methodsToGenerateFor, fn($x) => $x['name'] != "populate$modelOriginalName");
    //go through method, extract prefix, original method plural, original subject single
    $originalMethods = array_map(function($method) use($modelPlurals){
        if(!str_contains($method['name'], 'In')){
            echo "Warning: Could not determine original method for " . $method['name'] . " since it does not contain 'In'\n";
            return null;
        }
        [$left, $subjectSingular] = explode('In', $method['name']);
        //if left end with one of the model plurals, remove it, if none, throw exception
        foreach($modelPlurals as $modelPlural){
            if(str_ends_with($left, $modelPlural)){
                $methodPrefix = substr($left, 0, -strlen($modelPlural));
                $originalModelPlural = substr($left, strlen($methodPrefix));
                return [
                    'methodPrefix' => $methodPrefix,
                    'originalModelPlural' => $originalModelPlural,
                    'originalSubjectSingular' => $subjectSingular,
                    'parameters' => $method['parameters'],
                    'returnType' => $method['returnType']
                ];
            }
        }
        echo "Warning: Could not determine original method for " . $method['name'] . " with model plurals: " . implode(", ", $modelPlurals);
        return null;
        // throw new \Exception("Could not determine original method for " . $method['name'] . " with model plurals: " . implode(", ", $modelPlurals));
    }, $methodsToGenerateFor);

    return array_filter($originalMethods, fn($x) => $x !== null);
}

/**
 * Creates info items for which alias methods need to be made. Such as when only GetCactussesForCity exists, but GetCactiForCity needs to also exist.
 * @param array{methodPrefix: string, originalModelPlural: string, originalSubjectSingular: string, 
 *      parameters: array{
 *          type: string,
 *          name: string
 *     }, returnType: string} $originalMethods
 * @param string[] $modelPlurals
 * @return array{methodPrefix: string, originalModelPlural: string, originalSubjectSingular: string, 
 *      parameters: array{
 *          type: string,
 *          name: string
 *     }, returnType: string, original: mixed}
 */
function createAliasItems($originalMethods, $modelPlurals){
    //First create missing alternative plurals in the original methods, so that we have all variants
    //Create a lookup of the original methods by methodPrefix + originalModelPlural + originalSubjectSingular
    $originalMethodLookup = [];
    foreach($originalMethods as $method){
        $key = $method['methodPrefix'] . '|' . $method['originalModelPlural'] . '|' . $method['originalSubjectSingular'];
        $originalMethodLookup[$key] = $method;
    }

    $aliasMethods = [];
    //Try all variants for the model plurals, add them if they do not exist
    foreach($originalMethods as $method){
        foreach($modelPlurals as $modelPlural){
            $key = $method['methodPrefix'] . '|' . $modelPlural . '|' . $method['originalSubjectSingular'];
            if(!isset($originalMethodLookup[$key])){
                $aliasMethods[] = [
                    'methodPrefix' => $method['methodPrefix'],
                    'originalModelPlural' => $modelPlural,
                    'originalSubjectSingular' => $method['originalSubjectSingular'],
                    'parameters' => $method['parameters'],
                    'returnType' => $method['returnType'],
                    'original' => $method
                ];
            }
        }
    }
    return $aliasMethods;
}

/**
 * Summary of generateFullTrait
 * @param string $traitname
 * @param string $modelname
 * @param string[] $modelPlurals
 * @param array{
 *     name: string,
 *     parameters: array{
 *          type: string,
 *          name: string
 *     },
 *     returnType: string
 * } $methodsToGenerate $methodsToGenerate
 * @param array<string, string[]> $pluralLookup Mapping of singular to plural names.
 * @param array<string, string> $reversePluralLookup Mapping of plural to singular names.
 * @return array
 */
function generateFullProviderTrait($traitname, $modelname, $modelPlurals, $methodsToGenerateFor, $pluralLookup){
    echo "Generating trait: $traitname for provider of model: $modelname\n";
    $allMethodsCreated = [];
    //clean up the parameter types to use model names instead of full class names, also collect used models
    $usedModelsInParams = [$modelname];
    foreach($methodsToGenerateFor as &$method){
        foreach($method['parameters'] as &$param){
            $type = $param['type'];
            $match = tryExtractModelClassName($type);
            if($match !== null){
                $usedModelsInParams[] = $match;
                $param['type'] = $match;//in place change the type to the model name
            }
            else{
                continue;
            }
        }
    }
    $usedModelsInParams = array_unique($usedModelsInParams);

    //go through method, extract prefix, original method plural, original subject single
    $originalMethods = processMethods($methodsToGenerateFor, $modelPlurals, $modelname);
    
    //Get alias items for missing plural variants of the original methods based on the core models plurals
    $aliasItems = [];
    if(count($modelPlurals) >= 2){ //No need to create aliases if main model has only one plural, since no alternatives can exist.
        $aliasItems = createAliasItems($originalMethods, $modelPlurals);
    }

    $totalMethodsToGenerateFor = array_merge($originalMethods, $aliasItems);
    $totalMethodsToGenerateFor = array_filter($totalMethodsToGenerateFor, fn($x) => $x !== null);

    $fullAliasNames = array_map(fn($alias) => $alias['methodPrefix'] . $alias['originalModelPlural'] . 'In' . $alias['originalSubjectSingular'], $aliasItems);

    ob_start();
    $allMethodsCreated[] = fileTop($traitname, $modelname, $modelPlurals, $usedModelsInParams);
    //Aliases
    foreach($aliasItems as $aliasInfo){
        $original = $aliasInfo['original'];
        $allMethodsCreated[] = generateAlias($aliasInfo, $original);
    }

    //multi methods
    foreach($totalMethodsToGenerateFor as $methodInfo){
        $subjectSingular = $methodInfo['originalSubjectSingular'];
        $subjectPlurals = $pluralLookup[$subjectSingular];
        $abstractMethodName = $methodInfo['methodPrefix'] . $methodInfo['originalModelPlural'] . 'In' . $subjectSingular;
        if(!in_array($abstractMethodName, $fullAliasNames)){
            //Only generate abstract if not already generated as part of an alias
            generateAbstractOriginal($methodInfo['methodPrefix'] . $methodInfo['originalModelPlural'] . 'In' . $subjectSingular, $methodInfo['parameters']);//ide error about how the 6th arg is a string should not exist
        }
        foreach($subjectPlurals as $subjectPlural){
            $allMethodsCreated[] = generateMultiMethod(
                $methodInfo['methodPrefix'],
                $subjectSingular,
                $subjectPlural,
                $modelname,
                $methodInfo['originalModelPlural'],
                $methodInfo['parameters'] //ide error about how the 6th arg is a string should not exist
            );
        }
    }

    fileEnd();
    $generated = ob_get_clean();
    return [
        "trait" => $generated,
        "createdMethods" => $allMethodsCreated,
        "usedModels" => $usedModelsInParams
    ];
}