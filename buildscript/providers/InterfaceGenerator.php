<?php


namespace Buildscript\Providers;

use Buildscript\DataStructures\MethodDefinition;
use Buildscript\DataStructures\MethodParameter;

/**
 * @param string $interfacename
 * @param MethodDefinition[] $methods
 * @param string[] $usedModels
 * @return string
 */
function generateInterface(string $interfacename, array $methods, $usedModels):string{
    
    ob_start();
    ?>
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;

<?php
    foreach($usedModels as $usedModel){ ?>
use CanvasApiLibrary\Core\Models\<?=$usedModel?>;
<?php
}?>

/**
 * @template TSuccessResult
 * @template TUnauthorizedResult
 * @template TNotFoundResult
 */
interface <?=$interfacename?> extends HandleEmittedInterface{

    public function getClientID(): string;
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
 * @param MethodDefinition $method
 * @param string[] $usedModels
 * @return void
 */
function generateInterfaceMethod(MethodDefinition $method, $usedModels){
    // var_dump($method);
    $cleanedParams = array_map(function($x) use($usedModels)  {
        return new MethodParameter(
            $x->name,
            filterType($x->type, $usedModels),
            filterType($x->annotatedType, $usedModels)
        );
    }, $method->parameters);

    $returnTypeType = filterType($method->returnType->type, $usedModels);
    $returnTypeAnnotated = filterType($method->returnType->annotatedType, $usedModels);

    $paramstring = implode(", ", array_map(fn($x) => 
    $x->type . " $" . 
    $x->name, 
    $cleanedParams));
    $docstringParams = implode("\t\r ", array_map(fn($x) => "* @param " . $x->annotatedType . " $" . $x->name, $cleanedParams));
    ?>
    /**
    <?=$docstringParams?>

    * @return <?=$returnTypeAnnotated?>

    */
    public function <?=$method->name?>(<?=$paramstring?>) : <?=$returnTypeType?>;

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