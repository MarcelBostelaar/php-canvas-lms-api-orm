<?php

namespace Buildscript\Providers;
use Exception;
use function Buildscript\tryExtractModelClassName;
use Buildscript\DataStructures\MethodParameter;
use Buildscript\DataStructures\MethodReturnType;
use Buildscript\DataStructures\MethodDefinition;
use Buildscript\DataStructures\MethodInfo;
use Buildscript\DataStructures\PopulatorInfo;
use Buildscript\DataStructures\ProviderTraitResult;

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
 * @param MethodParameter[] $params 
 * @return void
 */
function generateAbstractOriginal($name, $params){
    $paramString = implode(', ', array_map(fn($p) => "{$p->type} \${$p->name}", $params));
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
 * @param string $modelname Original name of model
 * @param string $togenerate Plural name of model
 * @return MethodDefinition
 */
function generatePopulator($modelname, $togenerate): MethodDefinition {
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
    return new MethodDefinition(
        "populate$togenerate",
        [new MethodParameter(lcfirst($togenerate), "array", $modelname.'[]')],
        new MethodReturnType("array", $modelname.'[]')
    );
}

/**
 * Generates an alias function that just calls the original.
 * @param MethodInfo $aliasInfo
 * @param MethodInfo $original
 * @return MethodDefinition
 */
function generateAlias(MethodInfo $aliasInfo, MethodInfo $original): MethodDefinition {
    // var_dump($aliasInfo);
    $newMethodName = $aliasInfo->getMethodName();
    $originalMethodName = $original->getMethodName();
    $paramString = implode(', ', array_map(fn($p) => "{$p->type} \${$p->name}", $aliasInfo->parameters));
    $originalParamString = implode(', ', array_map(fn($p) => "\${$p->name}", $original->parameters));
    ?>

    /**
     * Alias of <?=$originalMethodName?>

     */
    public function <?=$newMethodName?>(<?=$paramString?>): <?=$aliasInfo->returnType->type?>{
        return $this-><?=$originalMethodName?>(<?=$originalParamString?>);
    }
    <?php
    return new MethodDefinition(
        $newMethodName,
        $aliasInfo->parameters,
        $aliasInfo->returnType
    );
}

/**
 * Generates a multi method for a given original.
 * Example: GetAllCactiForCity & GetAllCactiForCities (latter will be generated)
 * @param string $methodPrefix GetAll
 * @param string $originalSubjectName City
 * @param string $subjectPluralName Cities
 * @param string $modelName Cactus
 * @param string $modelPluralName Cacti
 * @param MethodParameter[] $params Unmodified params of the original. Will be inline replaced.
 * @return MethodDefinition
 */
function generateMultiMethod($methodPrefix, $originalSubjectName, $subjectPluralName, $modelName, $modelPluralName, $params): MethodDefinition {
    $multiParams = array_map(function($p) use ($originalSubjectName, $subjectPluralName){
        if($p->type === $originalSubjectName){
            return new MethodParameter(
                lcfirst($subjectPluralName),
                'array',
                $originalSubjectName . "[]",
                true
            );
        }
        return $p;
    }, $params);
    $multiMethodName = $methodPrefix . $modelPluralName . 'In' . $subjectPluralName;
    $originalMethodName = $methodPrefix . $modelPluralName . 'In' . $originalSubjectName;
    $originalParamString = implode(', ', array_map(fn($p) => $p->isArrayVariant ? '$' . lcfirst($originalSubjectName) : "\${$p->name}", $multiParams));
    $multiParamString = implode(', ', array_map(fn($p) => "{$p->type} \${$p->name}", $multiParams));
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
    return new MethodDefinition(
        $multiMethodName,
        $multiParams,
        new MethodReturnType("Lookup", "Lookup<$originalSubjectName, $modelName>")
    );
}

function fileEnd(){
    echo "}\n";
}

/**
 * Creates a structured info item for each method.
 * @param MethodDefinition[] $methodsToGenerateFor
 * @param array<string, string[]> $pluralLookup
 * @return MethodInfo[]
 */
function processXinYmethods($methodsToGenerateFor, $pluralLookup): array {
    $methodsToGenerateFor = array_filter($methodsToGenerateFor, fn($x) => !str_starts_with($x->name, "populate"));
    //go through method, extract prefix, original method plural, original subject single
    $originalMethods = array_map(function($method) use($pluralLookup){
        if(!str_contains($method->name, 'In')){
            echo "Warning: Could not determine original method for " . $method->name . " since it does not contain 'In'\n";
            return null;
        }
        [$left, $subjectSingular] = explode('In', $method->name);
        //if left end with one of the model plurals, remove it, if none, throw exception
        foreach($pluralLookup as $originalmodel => $modelPlurals){
            foreach($modelPlurals as $plural){
                if(str_ends_with($left, $plural)){
                    $methodPrefix = substr($left, 0, -strlen($plural));
                    $originalModelPlural = substr($left, strlen($methodPrefix));
                    return new MethodInfo(
                        $methodPrefix,
                        $originalModelPlural,
                        $subjectSingular,
                        $originalmodel,
                        $method->parameters,
                        $method->returnType
                    );
                }
            }
        }
        echo "Warning: Could not determine original method for " . $method->name . " with model plurals: " . implode(", ", $modelPlurals);
        return null;
        // throw new \Exception("Could not determine original method for " . $method->name . " with model plurals: " . implode(", ", $modelPlurals));
    }, $methodsToGenerateFor);

    return array_filter($originalMethods, fn($x) => $x !== null);
}

/**
 * @param MethodDefinition[] $methodsToGenerateFor
 * @param array<string, string[]> $modelPlurals
 * @return PopulatorInfo[]
 */
function processPopulateMethods($methodsToGenerateFor, $modelPlurals): array {
    $methodsToGenerateFor = array_filter($methodsToGenerateFor, fn($x) => str_starts_with($x->name, "populate"));
    $originalMethods = array_map(function($method) use($modelPlurals){
        // var_dump($method->name);
        $modelName = substr($method->name, strlen("populate"));
        if(!isset($modelPlurals[$modelName])){
            echo "Warning: Could not determine model of " . $method->name . ", tried to find: '" . $modelName . "' with model plurals: " . serialize($modelPlurals);
            return null;
        }
        return new PopulatorInfo(
            $modelName,
            $modelPlurals[$modelName]
        );
        // throw new \Exception("Could not determine original method for " . $method->name . " with model plurals: " . implode(", ", $modelPlurals));
    }, $methodsToGenerateFor);
    return array_filter($originalMethods, fn($x) => $x !== null);
}

/**
 * Creates info items for which alias methods need to be made. Such as when only GetCactussesForCity exists, but GetCactiForCity needs to also exist.
 * @param MethodInfo[] $originalMethods
 * @param array<string, string[]> $pluralLookup
 * @return MethodInfo[]
 */
function createAliasItems($originalMethods, $pluralLookup): array {
    $lookup = [];
    foreach($originalMethods as $original){
        $lookup[$original->getMethodName()] = true; 
    }
    //First create missing alternative plurals in the original methods, so that we have all variants

    $aliasMethods = [];
    //Try all variants for the model plurals, add them if they do not exist
    foreach($originalMethods as $method){
        foreach($pluralLookup[$method->originalModelSingular] as $modelPlural){
            $testMethodName = $method->methodPrefix . $modelPlural . $method->originalSubjectSingular;
            if(!isset($lookup[$testMethodName])){
                $aliasMethods[] = new MethodInfo(
                    $method->methodPrefix,
                    $modelPlural,
                    $method->originalSubjectSingular,
                    $method->originalModelSingular,
                    $method->parameters,
                    $method->returnType,
                    $method
                );
                $lookup[$testMethodName] = true;
            }
        }
    }
    return $aliasMethods;
}

/**
 * Summary of generateFullTrait
 * @param string $traitname
 * @param MethodDefinition[] $methodsToGenerateFor
 * @param array<string, string[]> $pluralLookup Mapping of singular to plural names.
 * @return ProviderTraitResult
 */
function generateFullProviderTrait($traitname, $methodsToGenerateFor, $pluralLookup): ProviderTraitResult {
    echo "Generating trait: $traitname\n";
    $allMethodsCreated = [];
    //clean up the parameter types to use model names instead of full class names, also collect used models
    $usedModelsInParams = [];//[$modelname];
    $cleanedMethods = [];
    foreach($methodsToGenerateFor as $method){
        $cleanedParams = [];
        foreach($method->parameters as $param){
            $type = $param->type;
            $match = tryExtractModelClassName($type);
            if($match !== null){
                $usedModelsInParams[] = $match;
                $cleanedParams[] = new MethodParameter($param->name, $match, $param->annotatedType, $param->isArrayVariant);
            }
            else{
                $cleanedParams[] = $param;
            }
        }
        $cleanedMethods[] = new MethodDefinition($method->name, $cleanedParams, $method->returnType);
    }
    $usedModelsInParams = array_unique($usedModelsInParams);

    //go through method, extract prefix, original method plural, original subject single
    $originalMethods = processXinYmethods($cleanedMethods, $pluralLookup);
    $populatorsToGenerate = processPopulateMethods($cleanedMethods, $pluralLookup);
    
    //Get alias items for missing plural variants of the original methods based on the core models plurals
    $aliasItems = createAliasItems($originalMethods, $pluralLookup);

    $totalMethodsToGenerateFor = array_merge($originalMethods, $aliasItems);
    $totalMethodsToGenerateFor = array_filter($totalMethodsToGenerateFor, fn($x) => $x !== null);

    $fullAliasNames = array_map(fn($alias) => $alias->getMethodName(), $aliasItems);

    ob_start();
    fileTop($traitname, $usedModelsInParams);
    //Aliases
    foreach($aliasItems as $aliasInfo){
        $original = $aliasInfo->original;
        $allMethodsCreated[] = generateAlias($aliasInfo, $original);
    }

    foreach($populatorsToGenerate as $populator){
        generateAbstractPopulator($populator->modelname);
        foreach($populator->generatePopulateMethods as $togenerate){
            $allMethodsCreated[] = generatePopulator($populator->modelname, $togenerate);
        }
    }

    //multi methods
    foreach($totalMethodsToGenerateFor as $methodInfo){
        $subjectSingular = $methodInfo->originalSubjectSingular;
        $subjectPlurals = $pluralLookup[$subjectSingular];
        $abstractMethodName = $methodInfo->getMethodName();
        if(!in_array($abstractMethodName, $fullAliasNames)){
            //Only generate abstract if not already generated as part of an alias
            generateAbstractOriginal($abstractMethodName, $methodInfo->parameters);//ide error about how the 6th arg is a string should not exist
        }
        foreach($subjectPlurals as $subjectPlural){
            $allMethodsCreated[] = generateMultiMethod(
                $methodInfo->methodPrefix,
                $subjectSingular,
                $subjectPlural,
                $methodInfo->originalSubjectSingular,
                $methodInfo->originalModelPlural,
                $methodInfo->parameters //ide error about how the 6th arg is a string should not exist
            );
        }
    }

    fileEnd();
    $generated = ob_get_clean();
    return new ProviderTraitResult(
        $generated,
        $allMethodsCreated,
        $usedModelsInParams
    );
}