<?php
namespace Buildscript\Models;

/**
 * @param string $classname
 * @param string[] $usedModels
 * @return void
 */
function fileTop($classname, array $usedModels){
    echo "<?php\n";
    ?>
/* Automatically generated based on model properties.*/
namespace CanvasApiLibrary\Models\Generated;

use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
<?php foreach($usedModels as $modelName): ?>
use CanvasApiLibrary\Models\<?=$modelName?>;
<?php endforeach ?>

trait <?=$classname?>{
<?php 
}

/**
 * @param string $type
 * @param string $name
 */
function nullableProperty($type, $name){
    return property("?$type", $name);
}

/**
 * @param string $type
 * @param string $name
 */
function property($type, $name){ ?>
    public <?=$type?> $<?=$name?>{
        get {
            return $this-><?=$name?>;
        }
        set(<?=$type?> $value) {
            $this-><?=$name?> = $value;
        }
    }
<?php 
}

/**
 * @param string $modelName
 * @param string $propertyName
 */
function NullableModelGetter($modelname, $propertyIdName){?>
        get {
            if($this-><?=$propertyIdName?> === null){
                return null;
            }
            $item = new <?=$modelname?>();
            $item->newFromMinimumDataRepresentation($this-><?=$propertyIdName?>);
            return $item;
        }
<?php
}

/**
 * @param string $modelName
 * @param string $propertyName
 */
function ModelGetter($modelname, $propertyIdName){
?>
        get { 
            $item = new <?=$modelname?>();
            $item->newFromMinimumDataRepresentation($this-><?=$propertyIdName?>);
            return $item;
        }
<?php
}

/**
 * @param string $modelName
 * @param string $propertyName
 * @param bool $nullable
 */
function modelProp($modelname, $propertyname, $nullable, $originalModelName){ 
    $intType = $nullable ? "?mixed" : "mixed";
    $modelType = ($nullable ? "?" : "") . $modelname;
    $propertyIdName = $propertyname . "_identity";
    ?>
    protected <?=$intType?> $<?=$propertyIdName?>;
    public <?=$modelType?> $<?=$propertyname?>{
<?= $nullable ? 
        NullableModelGetter($modelname,  $propertyIdName) 
        : modelGetter($modelname, $propertyIdName)?>
        set (<?=$modelType?> $value) {
<?php if($nullable): ?>
            if($value === null){
                $this-><?=$propertyIdName?> = null;
                return;
            }
<?php endif?>
            if($value->domain != $this->domain){
                $selfDomain = $this->domain->domain;
                $otherDomain = $value->domain->domain;
                throw new MixingDomainsException("Tried to save a <?=$modelname?> from domain '$otherDomain' to <?=$originalModelName?>.<?=$propertyname?> from domain '$selfDomain'.");
            }
            $this-><?=$propertyIdName?> = $value->getMinimumDataRepresentation();
        }
    }
<?php
}

function getSkeletonMethod($minimumProperties, $minimumModels, $modelName){
    ?>
    abstract public function getMinimumDataRepresentation();
    abstract public static function newFromMinimumDataRepresentation(mixed $data): static;
    <?php
}

function fileEnd(){
    echo "}";
}


/**
 * Summary of Buildscript\Models\GenerateFullModelTrait
 * @param mixed $chosenTraitName
 * @param array{type: string, name: string} $regularProperties
 * @param array{type: string, name: string} $nullableProperties
 * @param array{type: string, name: string} $minimumProperties
 * @param array{type: string, name: string} $minimumModelProperties
 * @param array{type: string, name: string} $ModelProperties
 * @param array{type: string, name: string} $nullableModelProperties
 * @return string
 */
function GenerateFullModelTrait($originalModelName, $chosenTraitName, array $regularProperties, array $nullableProperties, array $minimumProperties, array $minimumModelProperties, array $ModelProperties, array $nullableModelProperties){
    // var_dump($regularProperties);
    ob_start();
    $usedModels = array_unique(array_map(fn($x) => $x["type"], array_merge($ModelProperties, $nullableModelProperties, $minimumModelProperties)));
    array_push($usedModels, $originalModelName);

    fileTop($chosenTraitName, $usedModels);

    foreach($regularProperties as $p){
        property($p["type"], $p["name"]);
        echo "\n";
    }
    foreach($minimumProperties as $p){
        property($p["type"], $p["name"]);
        echo "\n";
    }
    
    foreach($nullableProperties as $p){
        nullableProperty($p["type"], $p["name"]);
        echo "\n";
    }

    foreach($ModelProperties as $p){
        modelProp($p["type"], $p["name"], false, $originalModelName);
        echo "\n";
    }

    foreach($minimumModelProperties as $p){
        modelProp($p["type"], $p["name"], false, $originalModelName);
        echo "\n";
    }

    foreach($nullableModelProperties as $p){
        modelProp($p["type"],  $p["name"], true, $originalModelName);
        echo "\n";
    }

    getSkeletonMethod($minimumProperties, $minimumModelProperties, $originalModelName);

    fileEnd();

    $generated = ob_get_clean();
    return $generated;
}