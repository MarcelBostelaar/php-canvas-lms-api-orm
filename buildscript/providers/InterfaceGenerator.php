<?php


namespace Buildscript\Providers;

use Buildscript\AtomicTypeDefinition;
use Buildscript\GenericTypeDefinition;
use Buildscript\MethodDefinition;
use Buildscript\MethodGenerationType;
use Buildscript\MethodParameter;
use Buildscript\TypeDefinitionBase;
use Buildscript\UnionTypeDefinition;

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

function replaceGenericsWithMixed(TypeDefinitionBase $type){
    if($type instanceof UnionTypeDefinition){
        if(count($type->types) !== 4){
            return $type;
        }
        $hasSuccess = false;
        $hasUnauthorized = false;
        $hasNotFound = false;
        $hasError = false;
        foreach($type->types as $subtype){
            if($subtype instanceof AtomicTypeDefinition){
                switch($subtype->type){
                    case "TUnauthorizedResult":
                        $hasUnauthorized = true;
                        break;
                    case "TNotFoundResult":
                        $hasNotFound = true;
                        break;
                    case "TErrorResult":
                        $hasError = true;
                        break;
                    default:
                        return $type;
                }
            }
            else if($subtype instanceof GenericTypeDefinition){
                if($subtype->type === "TSuccessResult"){
                    $hasSuccess = true;
                }
                else{
                    return $type;
                }
            }
            else{
                return $type;
            }
        }
        if($hasSuccess && $hasUnauthorized && $hasNotFound && $hasError){
            return new AtomicTypeDefinition("mixed");
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
                    $p->type->map(fn($t) => replaceStandardUnionWithGeneric($t))
                );
            }, $x->parameters),
            $x->returnType->map(fn($t) => replaceStandardUnionWithGeneric($t)),
            MethodGenerationType::InterfaceMethod
        );
    }, $methods);

    ob_start();
    ?>
//Auto-generated file, changes will be lost
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
 * @template TSuccessResult The type that a successful result will emit, which itself should be a class with a generic type.
 * @template TUnauthorizedResult Type of value that an unauthorized result will emit
 * @template TNotFoundResult Type of value that a not found result will emit
 * @template TErrorResult Type of value that any other error result will emit
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
<?=$method->createDocstringParamsAndReturn(1)?>

    */
    public function <?=$method->name?>(<?=$method->paramString()?>) : <?=$method->returnType
    ->map(fn($x) => replaceGenericsWithMixed($x))->codeString()?>;

<?php
}