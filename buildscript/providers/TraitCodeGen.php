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
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
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
function generateAbstractOriginal(MethodDefinition $method){
    ?>
    abstract public function <?=$method->name?>(<?=$method->paramString()?>) : <?=$method->returnType->codeString()?>;
<?php
}


function generatePopulator(MethodDefinition $method) {
    if($method->generationType !== MethodGenerationType::PopulateMultiple){
        throw new Exception("Method is not of type PopulateMultiple: " . $method->name);
    }
    $tailParams = array_slice($method->parameters, 1);
    $tailParamString = implode(', ', array_map(fn($p) => ' $' . $p->name, $tailParams));
    ?>
    /**
     * Summary of <?=$method->name . "\n"?>
     * This is a plural version of <?=$method->pluralVariantOf->name . "\n"?>
<?=$method->createDocstringParamsAndReturn(1) . "\n"?>
     */
    public function <?=$method->name?>(<?=$method->paramString()?>): <?= $method->returnType->codeString() ?> {
        $results = [];
        foreach($<?=$method->parameters[0]->name?> as $item){
            $result = $this-><?=$method->pluralVariantOf->name?>($item,<?= $tailParamString ?>);
            if(!$result instanceof SuccessResult){
                return $result;
            }
            $results[] = $result->value;
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
     * Alias of <?=$aliasInfo->aliasOf->name . "\n"?>
<?=$aliasInfo->createDocstringParamsAndReturn(1) . "\n"?>
     */
    public function <?=$aliasInfo->name?>(<?=$aliasInfo->paramString()?>): <?=$aliasInfo->returnType->codeString()?> {
        return $this-><?=$aliasInfo->aliasOf->name?>(<?=implode(', ', array_map(fn($p) => '$' . $p->name, $aliasInfo->parameters))?>);
    }
    <?php
}


function generateMultiMethod(MethodDefinition $method) {
    if($method->generationType !== MethodGenerationType::GetItemsInMultiple){
        throw new Exception("Method is not of type GetItemsForMultiple: " . $method->name);
    }

    $relevantParam = array_filter($method->parameters, fn(MethodParameter $p) => $p->name === $method->metadata['relevantParam']);

    if(count($relevantParam) !== 1){
        throw new Exception("Could not determine relevant parameter for multi method: " . $method->name);
    }
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
    echo "\n}\n";
}


/**
 * Summary of generateFullTrait
 * @param string $traitname
 * @param MethodDefinition[] $methodsToGenerateFor
 * @param array<string, string[]> $pluralLookup Mapping of singular to plural names.
 * @return ProviderTraitResult
 */
function generateFullProviderTrait($traitname, $methodsToGenerateFor, $pluralLookup, $usedModels): ProviderTraitResult {
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
    $allNewMethods = array_unique(array_merge($aliasesOfOriginals, $multiVersions), SORT_REGULAR);


    ob_start();
    fileTop($traitname, $usedModels);

    foreach($methodsToGenerateFor as $method){
        generateAbstractOriginal($method);
    }

    foreach($allNewMethods as $newMethod){
        if($method->aliasOf !== null){
            generateAlias($newMethod);
            continue;
        }
        if($newMethod->generationType === MethodGenerationType::GetItemsInSingle || $newMethod->generationType === MethodGenerationType::PopulateSingle){
            throw new Exception("New non alias or non multiple variant in trait generation not supported: " . $newMethod->name);
        }        
        if($newMethod->generationType === MethodGenerationType::GetItemsInMultiple){
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