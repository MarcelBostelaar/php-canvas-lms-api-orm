<?php
namespace Buildscript\Models;

use DateTime;
/**
 * @param string $classname
 * @param string[] $usedModels
 * @return void
 */
function fileTop($classname, array $usedModels){
    echo "<?php\n";
    ?>
/* Automatically generated based on model properties.*/
namespace Src\Models\Generated;

use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Models\Domain;
<?php foreach($usedModels as $modelName): ?>
use CanvasApiLibrary\Models\<?=$modelName?>;
<?php endforeach ?>

trait <?=$classname?>{
    abstract protected function getDomain(): Domain;

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
function NullableModelGetter($modelname, $propertyname, $propertyIdName){?>
        get {
            return $this-><?=$propertyname?> ? new <?=$modelname?>($this->getDomain(), $this-><?=$propertyIdName?>) : null;
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
            return new <?=$modelname?>($this->getDomain(), $this-><?=$propertyIdName?>);
        }
<?php
}

/**
 * @param string $modelName
 * @param string $propertyName
 * @param bool $nullable
 */
function modelProp($modelname, $propertyname, $nullable){ 
    $intType = $nullable ? "?int" : "int";
    $modelType = ($nullable ? "?" : "") . $modelname;
    $intPropertyName = $propertyname . "_id";
    ?>
    protected <?=$intType?> $<?=$intPropertyName?>;
    public <?=$modelType?> $<?=$propertyname?>{
<?= $nullable ? 
        NullableModelGetter($modelname, $propertyname, $intPropertyName) 
        : modelGetter($modelname, $intPropertyName)?>
        set (<?=$modelType?> $value) {
<?php if($nullable): ?>
            if($value === null){
                $this-><?=$intPropertyName?> = null;
                return;
            }
<?php endif?>
            if($value->getDomain() != $this->getDomain()){
                $classname = self::class;
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a '$classname' from domain '$otherDomain' to <?=$modelname?>.<?=$propertyname?> from domain '$selfDomain'.");
            }
            $this-><?=$intPropertyName?> = $value->id;
        }
    }
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
 * @param array{type: string, name: string} $ModelProperties
 * @param array{type: string, name: string} $nullableModelProperties
 * @return string
 */
function GenerateFullModelTrait($chosenTraitName, array $regularProperties, array $nullableProperties, array $ModelProperties, array $nullableModelProperties){
    var_dump($regularProperties);
    ob_start();
    $usedModels = array_unique(array_map(fn($x) => $x["type"], array_merge($ModelProperties, $nullableModelProperties)));
    fileTop($chosenTraitName, $usedModels);

    foreach($regularProperties as $p){
        property($p["type"], $p["name"]);
        echo "\n";
    }

    foreach($nullableProperties as $p){
        nullableProperty($p["type"], $p["name"]);
        echo "\n";
    }

    foreach($ModelProperties as $p){
        modelProp($p["type"], $p["name"], false);
        echo "\n";
    }

    foreach($nullableModelProperties as $p){
        modelProp($p["type"],  $p["name"], true);
        echo "\n";
    }

    fileEnd();

    $generated = ob_get_clean();
    return $generated;
}