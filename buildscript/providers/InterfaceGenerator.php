<?php


namespace Buildscript\Providers;

use Buildscript\AtomicTypeDefinition;
use Buildscript\GenericTypeDefinition;
use Buildscript\MethodDefinition;
use Buildscript\MethodGenerationType;
use Buildscript\MethodParameter;
use Buildscript\TypeDefinitionBase;

function replaceStandardUnionWithGeneric(TypeDefinitionBase $type){
    if($type instanceof AtomicTypeDefinition){
        $realtype = $type->type;
        if(str_contains($type->type, "\\")){
            $parts = explode("\\", $type->type);
            $realtype = array_pop($parts);
        }
        switch($realtype){
            case "SuccessResult":
                return new AtomicTypeDefinition("TSuccessResult");
            case "UnauthorizedResult":
                return new AtomicTypeDefinition("TUnauthorizedResult");
            case "NotFoundResult":
                return new AtomicTypeDefinition("TNotFoundResult");
            case "ErrorResult":
                return new AtomicTypeDefinition("TErrorResult");
            default:
                return $type;
        }
    }
    if($type instanceof GenericTypeDefinition){
        if($type->type === "SuccessResult"){
            $copy = clone $type;
            $copy->type = "TSuccessResult";
            return $copy;
        }
    }
    return $type;
}

/**
 * @param string $interfacename
 * @param MethodDefinition[] $methods
 * @param string[] $usedModels
 * @return string
 */
function generateInterface(string $interfacename, array $methods, $usedModels):string{
    $methods = array_map(function($x){
        return new MethodDefinition(
            $x->name,
            $x->docstring,
            array_map(function($p){
                return new MethodParameter(
                    $p->name,
                    $p->type->map(fn($t) => replaceStandardUnionWithGeneric($t)),
                    $p->annotatedType->map(fn($t) => replaceStandardUnionWithGeneric($t))
                );
            }, $x->parameters),
            new \Buildscript\MethodReturnType(
                $x->returnType->type->map(fn($t) => replaceStandardUnionWithGeneric($t)),
                $x->returnType->annotatedType->map(fn($t) => replaceStandardUnionWithGeneric($t))
            ),
            MethodGenerationType::InterfaceMethod
        );
    }, $methods);

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
 * @template TUnauthorizedResult //TODO change this
 * @template TNotFoundResult
 */
interface <?=$interfacename?> extends HandleEmittedInterface{

    public function getClientID(): string;
<?php
foreach($methods as $method){
    generateInterfaceMethod($method);
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
function generateInterfaceMethod(MethodDefinition $method){
    
    ?>
    /**
    <?=$method->docstring?>
    <?=$method->createDocstringParamsAndReturn(1)?>
    */
    public function <?=$method->name?>(<?=$method->paramString()?>) : <?=(string)$method->returnType->type?>;

<?php
}