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
use CanvasApiLibrary\Models\Domain;
<?php foreach($usedModels as $modelName): ?>
use CanvasApiLibrary\Models\<?=$modelName?>;
<?php endforeach ?>

trait <?=$classname?>{
    abstract public function getDomain(): Domain;

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
            if($this-><?=$propertyname?> === null){
                return null;
            }
            $item = new <?=$modelname?>($this->getDomain());
            $item->id = $this-><?=$propertyIdName?>;
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
            $item = new <?=$modelname?>($this->getDomain());
            $item->id = $this-><?=$propertyIdName?>;
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
            if($value->getDomain()->domain != $this->getDomain()->domain){
                $selfDomain = $this->getDomain()->domain;
                $otherDomain = $value->getDomain()->domain;
                throw new MixingDomainsException("Tried to save a <?=$modelname?> from domain '$otherDomain' to <?=$originalModelName?>.<?=$propertyname?> from domain '$selfDomain'.");
            }
            $this-><?=$intPropertyName?> = $value->id;
        }
    }
<?php
}

function getSkeletonMethod($minimumProperties, $minimumModels, $modelName){
    array_push($minimumProperties, ["type" => "int", "name" => "id"]);
    
    $mergedNames = array_map(fn($x) => $x['name'],array_merge($minimumModels, $minimumProperties));

    $propsProcessed = array_map(fn($x) => "['" . $x['name'] . "'] => \$this->" . $x['name'], $minimumProperties);
    $modelsProcessed = array_map(fn($x) => "['" . $x['name'] . "'] => \$this->" . $x['name'] . "->getMinimumDataRepresentation()", $minimumModels);
    $assocArrayStringInside = implode(",\n", array_merge($propsProcessed, $modelsProcessed));
    ?>
    public function getMinimumDataRepresentation(){
        if(!(<?php foreach($mergedNames as $name){?>
            isset($this-><?=$name?>) &&
            <?php }?>
            true
        )){
            throw new NotPopulatedException("Not all minimum required fields for this model, so it can be re-populated, have been set.");
        }
        return [
            <?=$assocArrayStringInside?>
        ];
    }

    public static function newFromMinimumDataRepresentation(Domain $domain, array $data): <?=$modelName?>{
        if(!(<?php foreach($mergedNames as $name){?>
            isset($data['<?=$name?>']) &&
            <?php }?>
            true
        )){
            throw new NotPopulatedException("Not all minimum required fields for this model are in the data provided.");
        }
        $newInstance = new <?=$modelName?>($domain);
        <?php foreach ($minimumProperties as $prop){?>
        $newInstance-><?=$prop['name']?> = $data['<?=$prop['name']?>'];
        <?php }?>

        <?php foreach ($minimumModels as $prop){?>
        $newInstance-><?=$prop['name']?> = <?=$prop['type']?>::newFromMinimumDataRepresentation($data['<?=$prop['name']?>']);
        <?php }?>
        return $newInstance;
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