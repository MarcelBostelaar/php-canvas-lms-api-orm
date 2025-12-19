<?php

namespace Buildscript\Providers;
use Buildscript\AtomicTypeDefinition;
use Buildscript\MethodGenerationType;
use Exception;
use function Buildscript\tryExtractModelClassName;
use Buildscript\MethodParameter;
use Buildscript\MethodReturnType;
use Buildscript\MethodDefinition;
use Buildscript\MethodInfo;
use Buildscript\PopulatorInfo;
use Buildscript\ProviderTraitResult;

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


function generatePopulator(MethodDefinition $method) {
    if($method->pluralVariantOf === null){
        throw new Exception("Cannot generate populator for plural variant method: " . $method->name);
    }
    if($method->generationType !== MethodGenerationType::PopulateMultiple){
        throw new Exception("Method is not of type PopulateMultiple: " . $method->name);
    }
    if(count($method->parameters) !== 1){
        throw new Exception("Populate multiple method must have exactly one parameter: " . $method->name);
    }

    ?>
    /**
     * Summary of <?=$method->name?>
     * This is a plural version of <?=$method->pluralVariantOf->name?>
     <?=$method->createDocstringParamsAndReturn()?>
     */
    public function <?=$method->name?>(array $<?=$method->parameters[0]->name?>): <?= $method->returnType->type ?> {
        $results = [];
        foreach($<?=$method->parameters[0]->name?> as $item){
            $result = $this-><?=$method->pluralVariantOf->name?>($item);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->data;
        }
        return new SuccessResult($results);
    }
    
    <?php
}

/**
 * Generated an alias that just calls its original.
 * @param MethodDefinition $aliasInfo
 */
function generateAlias(MethodDefinition $aliasInfo) {
    if($aliasInfo->aliasOf === null){
        throw new Exception("Cannot generate alias for method without aliasOf set: " . $aliasInfo->name);
    }

    ?>
    /**
     * Alias of <?=$aliasInfo->aliasOf->name?>
     <?=$aliasInfo->createDocstringParamsAndReturn()?>
     */
    public function <?=$aliasInfo->name?>(<?=$aliasInfo->paramString()?>): <?=$aliasInfo->returnType->type?> {
        return $this-><?=$aliasInfo->aliasOf->name?>(<?=implode(', ', array_map(fn($p) => '$' . $p->name, $aliasInfo->parameters))?>);
    }

    <?php
}


function generateMultiMethod(MethodDefinition $method) {
    if($method->pluralVariantOf === null){
        throw new Exception("Cannot generate multi method for a plural variant method: " . $method->name);
    }
    if($method->generationType !== MethodGenerationType::GetItemsForMultiple){
        throw new Exception("Method is not of type GetItemsForMultiple: " . $method->name);
    }

    $relevantParam = array_filter($method->parameters, fn(MethodParameter $p) => $p->type->isArrayVariant);

    if(count($relevantParam) !== 1){
        throw new Exception("Could not determine relevant parameter for multi method: " . $method->name);
    }
    /** @var MethodParameter $relevantParam */
    $relevantParam = array_values($relevantParam)[0];

    ?>
    /**
     * Summary of <?=$method->name?>
     * This is a plural version of <?=$method->pluralVariantOf->name?>
     <?=$method->createDocstringParamsAndReturn()?>
     */
    public function <?=$method->name?>(<?=$method->paramString()?>): Lookup{
        $lookup = new Lookup();
        foreach($<?=$relevantParam->name?> as $x){
            $lookup->add($x, $this-><?=$method->pluralVariantOf->name?>($x));
        }
        return $lookup;
    }
<?php
}

function fileEnd(){
    echo "}\n";
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

    $aliasesOfOriginals = array_map(function(MethodDefinition $method) use ($pluralLookup){
            return $method->getAliasForms($pluralLookup);
        }, $methodsToGenerateFor);
    /** @var MethodDefinition[] */
    $aliasesOfOriginals = array_merge(...$aliasesOfOriginals);

    $multiVersions = array_map(function(MethodDefinition $method) use ($pluralLookup){
            return $method->createPluralVariants($pluralLookup);
        }, $methodsToGenerateFor);
    /** @var MethodDefinition[] */
    $multiVersions = array_merge(...$multiVersions);

    /** @var MethodDefinition[] */
    $allNewMethods = array_unique(array_merge($aliasesOfOriginals, $multiVersions));


    ob_start();
    fileTop($traitname, []);

    foreach($methodsToGenerateFor as $method){
        generateAbstractOriginal($method->name, $method->parameters);
    }

    foreach($allNewMethods as $newMethod){
        if($method->aliasOf !== null){
            generateAlias($newMethod);
            continue;
        }
        if($newMethod->generationType === MethodGenerationType::GetItemsForSingle || $newMethod->generationType === MethodGenerationType::PopulateSingle){
            throw new Exception("New non alias or non multiple variant in trait generation not supported: " . $newMethod->name);
        }        
        if($newMethod->generationType === MethodGenerationType::GetItemsForMultiple){
            generateMultiMethod($newMethod);
            continue;
        }
        if($newMethod->generationType === MethodGenerationType::PopulateMultiple){
            generatePopulator($newMethod);
            continue;
        }
        throw new Exception("Unknown generation type for method: " . $newMethod->name);
    }
    fileEnd();
    $generated = ob_get_clean();
    return new ProviderTraitResult(
        $generated,
        $allNewMethods,
        []
    );
}