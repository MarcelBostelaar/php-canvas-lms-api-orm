<?php


namespace Buildscript\Providers;

/**
 * 
 * @param string $interfacename
 * @param array{
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
 * }[] $methods
 * @return void
 */
function generateInterface(string $interfacename, array $methods, $usedModels):string{
    
    ob_start();
    ?>
namespace CanvasApiLibrary\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Providers\Utility\HandleEmittedInterface;

<?php
    foreach($usedModels as $usedModel){ ?>
use CanvasApiLibrary\Models\<?=$usedModel?>;
<?php
}?>

interface <?=$interfacename?> extends HandleEmittedInterface{

<?php
foreach($methods as $method){
    generateInterfaceMethod($method, $usedModels);
}
?>
}
<?php

    $generated = ob_get_clean();
    return $generated;
}

/**
 * 
 * @param array{
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
 * } $method
 * @return void
 */
function generateInterfaceMethod(array $method, $usedModels){
    // var_dump($method);
    $method["parameters"] = array_map(function($x) use($usedModels)  {
        return [
        "name" => $x["name"],
        "type" => filterType($x["type"], $usedModels),
        "annotatedtype" => filterType($x["annotatedtype"], $usedModels)
        ];
    }, $method["parameters"]);

    $method['returnType']['type'] = filterType($method['returnType']["type"], $usedModels);
    $method['returnType']['annotatedtype'] = filterType($method['returnType']["annotatedtype"], $usedModels);

    $paramstring = implode(", ", array_map(fn($x) => 
    $x["type"] . " $" . 
    $x["name"], 
    $method["parameters"]));
    $docstringParams = implode("\t\r ", array_map(fn($x) => "* @param " . $x["annotatedtype"] . " $" . $x["name"], $method["parameters"]));
    ?>
    /**
    <?=$docstringParams?>

    * @return <?=$method['returnType']['annotatedtype']?>

    */
    public function <?=$method["name"]?>(<?=$paramstring?>) : <?=$method['returnType']['type']?>;

<?php
}

function filterType($original, $usedModels){
    $split = explode("\\",$original);
    $item = array_pop($split);
    if(in_array($item, $usedModels)){
        return $item;
    }
    return $original;
}