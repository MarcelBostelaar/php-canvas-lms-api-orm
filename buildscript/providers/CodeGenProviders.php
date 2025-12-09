<?php

namespace Buildscript\Providers;
use Exception;
use function Buildscript\tryExtractModelClassName;

/**
 * Genereates the start of the class.
 * @param string $traitname
 * @param string $modelname
 * @param string[] $modelPlurals
 * @param string[] $usedModels
 * @return void
 */
function fileTop($traitname, $usedModels){
?>
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Core\Providers\Generated\Traits;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
<?php
    foreach($usedModels as $usedModel){ ?>
use CanvasApiLibrary\Core\Models\<?=$usedModel?>;
<?php
    }
?>

trait <?=$traitname?>{
    
    
<?php
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

function generateAbstractPopulator($modelname){
    ?>
    
    abstract public function populate<?=$modelname?>(<?=$modelname . '$' . lcfirst($modelname)?>);
    <?php
}

/**
 * Summary of Buildscript\Providers\generatePopulator
 * @param mixed $modelname Original name of model
 * @param mixed $togenerate Plural name of model
 * @return array{name: string, parameters: array, returnType: array{annotatedtype: string, type: string}}
 */
function generatePopulator($modelname, $togenerate){
    $param = '$' . lcfirst($togenerate);
    ?>

    /**
    * Plural version of populate<?=$modelname?>

    * @param <?=$modelname?>[] <?=$param?>

    * @return <?=$modelname?>[]

    */
    public function populate<?=$togenerate?>(array <?=$param?>) : array{
        return array_map(fn($x) => $this->populate<?=$modelname?>($x), <?=$param?>);
    }
    
    <?php
    return [
        "name" => "populate$togenerate",
        "parameters" => [[
            "name" => lcfirst($togenerate),
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
    // var_dump($aliasInfo);
    $newMethodName = $aliasInfo['methodPrefix'] . $aliasInfo['originalModelPlural'] . 'In' . $aliasInfo['originalSubjectSingular'];
    $originalMethodName = $original['methodPrefix'] . $original['originalModelPlural'] . 'In' . $original['originalSubjectSingular'];
    $paramString = implode(', ', array_map(fn($p) => "{$p['type']} \${$p['name']}", $aliasInfo['parameters']));
    $originalParamString = implode(', ', array_map(fn($p) => "\${$p['name']}", $original['parameters']));
    ?>

    /**
     * Alias of <?=$originalMethodName?>

     */
    public function <?=$newMethodName?>(<?=$paramString?>): <?=$aliasInfo['returnType']["type"]?>{
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
function processXinYmethods($methodsToGenerateFor, $pluralLookup){
    $methodsToGenerateFor = array_filter($methodsToGenerateFor, fn($x) => !str_starts_with($x['name'], "populate"));
    //go through method, extract prefix, original method plural, original subject single
    $originalMethods = array_map(function($method) use($pluralLookup){
        if(!str_contains($method['name'], 'In')){
            echo "Warning: Could not determine original method for " . $method['name'] . " since it does not contain 'In'\n";
            return null;
        }
        [$left, $subjectSingular] = explode('In', $method['name']);
        //if left end with one of the model plurals, remove it, if none, throw exception
        foreach($pluralLookup as $originalmodel => $modelPlurals){
            foreach($modelPlurals as $plural){
                if(str_ends_with($left, $plural)){
                    $methodPrefix = substr($left, 0, -strlen($plural));
                    $originalModelPlural = substr($left, strlen($methodPrefix));
                    return [
                        'methodPrefix' => $methodPrefix,
                        'originalModelPlural' => $originalModelPlural,
                        'originalSubjectSingular' => $subjectSingular,
                        'originalModelSingular' => $originalmodel,
                        'parameters' => $method['parameters'],
                        'returnType' => $method['returnType']
                    ];
                }
            }
        }
        echo "Warning: Could not determine original method for " . $method['name'] . " with model plurals: " . implode(", ", $modelPlurals);
        return null;
        // throw new \Exception("Could not determine original method for " . $method['name'] . " with model plurals: " . implode(", ", $modelPlurals));
    }, $methodsToGenerateFor);

    return array_filter($originalMethods, fn($x) => $x !== null);
}

function processPopulateMethods($methodsToGenerateFor, $modelPlurals){
    $methodsToGenerateFor = array_filter($methodsToGenerateFor, fn($x) => str_starts_with($x['name'], "populate"));
    $originalMethods = array_map(function($method) use($modelPlurals){
        // var_dump($method["name"]);
        $modelName = substr($method["name"], strlen("populate"));
        if(!isset($modelPlurals[$modelName])){
            echo "Warning: Could not determine model of " . $method['name'] . ", tried to find: '" . $modelName . "' with model plurals: " . serialize($modelPlurals);
            return null;
        }
        return [
            "modelname" => $modelName,
            "generatePopulateMethods" => $modelPlurals[$modelName]
        ];
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
 * @param array $pluralLookup
 * @return array{methodPrefix: string, originalModelPlural: string, originalSubjectSingular: string, 
 *      parameters: array{
 *          type: string,
 *          name: string
 *     }, returnType: string, original: mixed}
 */
function createAliasItems($originalMethods, $pluralLookup){
    $lookup = [];
    foreach($originalMethods as $original){
        $lookup[$original["methodPrefix"] . $original["originalModelPlural"] . $original["originalSubjectSingular"]] = true; 
    }
    //First create missing alternative plurals in the original methods, so that we have all variants

    $aliasMethods = [];
    //Try all variants for the model plurals, add them if they do not exist
    foreach($originalMethods as $method){
        foreach($pluralLookup[$method["originalModelSingular"]] as $modelPlural){
            if(!isset($lookup[$original["methodPrefix"] . $modelPlural . $original["originalSubjectSingular"]])){
                $aliasMethods[] = [
                    'methodPrefix' => $method['methodPrefix'],
                    'originalModelPlural' => $modelPlural,
                    'originalModelSingular' => $method['originalModelSingular'],
                    'originalSubjectSingular' => $method['originalSubjectSingular'],
                    'parameters' => $method['parameters'],
                    'returnType' => $method['returnType'],
                    'original' => $method
                ];
                $lookup[$original["methodPrefix"] . $modelPlural . $original["originalSubjectSingular"]] = true;
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
function generateFullProviderTrait($traitname, $methodsToGenerateFor, $pluralLookup){
    echo "Generating trait: $traitname\n";
    $allMethodsCreated = [];
    //clean up the parameter types to use model names instead of full class names, also collect used models
    $usedModelsInParams = [];//[$modelname];
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
    $originalMethods = processXinYmethods($methodsToGenerateFor, $pluralLookup);
    $populatorsToGenerate = processPopulateMethods($methodsToGenerateFor, $pluralLookup);
    
    //Get alias items for missing plural variants of the original methods based on the core models plurals
    $aliasItems = createAliasItems($originalMethods, $pluralLookup);

    $totalMethodsToGenerateFor = array_merge($originalMethods, $aliasItems);
    $totalMethodsToGenerateFor = array_filter($totalMethodsToGenerateFor, fn($x) => $x !== null);

    $fullAliasNames = array_map(fn($alias) => $alias['methodPrefix'] . $alias['originalModelPlural'] . 'In' . $alias['originalSubjectSingular'], $aliasItems);

    ob_start();
    fileTop($traitname, $usedModelsInParams);
    //Aliases
    foreach($aliasItems as $aliasInfo){
        $original = $aliasInfo['original'];
        $allMethodsCreated[] = generateAlias($aliasInfo, $original);
    }

    foreach($populatorsToGenerate as $populator){
        generateAbstractPopulator($populator['modelname']);
        foreach($populator['generatePopulateMethods'] as $togenerate){
            $allMethodsCreated[] = generatePopulator($populator["modelname"], $togenerate);
        }
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
                $methodInfo['originalSubjectSingular'],
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