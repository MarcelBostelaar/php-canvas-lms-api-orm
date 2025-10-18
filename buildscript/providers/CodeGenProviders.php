<?php

namespace Buildscript\Providers;

function fileTop($traitname, $modelname, $modelPlurals, $usedModels){
?>

namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Providers\Utility\Lookup;
<?php
    foreach($usedModels as $usedModel){ ?>
use CanvasApiLibrary\Models\<?=$usedModel?>;
<?php
    }
?>

trait <?=$traitname?>{
    abstract public function populate<?=$modelname?>(<?=$modelname?> $<?=strtolower($modelname)?>);
<?php
    //Generate a plural variant for each plural name variant.
    foreach($modelPlurals as $pluralName){?>
    /**
     * Array variant of populate<?=$modelname?>
     * @param <?=$modelname?>[] $<?=strtolower($pluralName)?>
     * @return <?=$modelname?>[]
     */
    public function populate<?=$pluralName?>(array $<?=strtolower($pluralName)?>): array{
        return array_map(fn($x) => $this->populate<?=$modelname?>($x), $<?=strtolower($pluralName)?>);
    }

<?php
    }
}

/**
 * Generates an abstract method that can be found in the original provider.
 * Example: GetAllCactiForCity & GetAllCactiForCities (former will be generated)
 * @param string $name GetAllCactiForCity
 * @param array{array{
 *  name: string,
 *  type: string
 * }} $params 
 * @param string $modelName Cactus
 * @return void
 */
function generateAbstractOriginal($name, $params, $modelName){

}

/**
 * Generates a multi method for a given original.
 * Example: GetAllCactiForCity & GetAllCactiForCities (latter will be generated)
 * @param string $singleMethodName GetAllCactiForCity
 * @param string $multiMethodName GetAllCactiForCities
 * @param string $modelName Cactus
 * @param array{array{
 *  name: string,
 *  type: string
 * }} $params Unmodified params of the original. Will be inline replaced.
 * @return void
 */
function generateMultiMethod($methodPrefix, $originalSubjectName, $subjectPluralName, $modelName, $params){

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
 * @return string
 */
function generateFullTrait($traitname, $modelname, $modelPlurals, $methodsToGenerate){
    ob_start();

    $generated = ob_get_clean();
    return $generated;
}