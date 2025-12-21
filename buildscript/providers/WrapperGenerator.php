<?php

namespace Buildscript\Providers;

use Buildscript\AtomicTypeDefinition;
use Buildscript\GenericTypeDefinition;
use Buildscript\MethodDefinition;
use Buildscript\MethodGenerationType;
use Buildscript\MethodParameter;
use Buildscript\TypeDefinitionBase;
use Buildscript\UnionTypeDefinition;



/**
 * @param string $interfacename
 * @param MethodDefinition[] $methods
 * @param string[] $usedModels
 * @return string
 */
function generateWrapper(string $wrappername, string $interfacename, array $methods, $usedModels): string{
    //filter to only methods that return union type:
    $methodsProcessed = [];
    foreach($methods as $method){
        $returnWrapped = ExtractProviderMethodsVisitor::extractUnionWrappedType($method->returnType);
        if($returnWrapped === null){
            $methodsProcessed[] = $method;
        }
        else{
            $method = clone $method;
            $method->returnType = new UnionTypeDefinition([
                new AtomicTypeDefinition('TSuccessResult2'),
                new AtomicTypeDefinition('TErrorResult2'),
                new AtomicTypeDefinition('TNotFoundResult2'),
                new AtomicTypeDefinition('TUnauthorizedResult2')
            ]);
            $methodsProcessed[] = $method;
        }
    }
    $methods = $methodsProcessed;
    ob_start();
    ?>
//Auto-generated file, changes will be lost
namespace CanvasApiLibrary\Core\Providers\Interfaces;

use CanvasApiLibrary;
use CanvasApiLibrary\Core\Providers\Utility\Lookup;
use CanvasApiLibrary\Core\Providers\Utility\HandleEmittedInterface;
use Closure;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;

<?php
    foreach($usedModels as $usedModel){ ?>
use CanvasApiLibrary\Core\Models\<?=$usedModel?>;
<?php
}?>

/**
 * @template TSuccessResult Wrapped success type
 * @template TSuccessResult2 Returned success type
 * @template TUnauthorizedResult Wrapped type of value that an unauthorized result will emit
 * @template TUnauthorizedResult2 Returned type of value that an unauthorized result will emit
 * @template TNotFoundResult Wrapped type of value that a not found result will emit
 * @template TNotFoundResult2 Returned type of value that a not found result will emit
 * @template TErrorResult Wrapped type of value that any other error result will emit
 * @template TErrorResult2 Returned type of value that any other error result will emit
 * @implements <?=$interfacename?><TSuccessResult2,TErrorResult2,TNotFoundResult2,TUnauthorizedResult2>
 */
class <?=$wrappername?> implements <?=$interfacename?> {

    /**
     * Summary of __construct
     * @param <?=$interfacename?><TSuccessResult,TErrorResult,TNotFoundResult,TUnauthorizedResult> $innerProvider
     * @param Closure(TSuccessResult|TErrorResult|TNotFoundResult|TUnauthorizedResult) : (TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2) $resultProcessor
     */
    public function __construct(
        private <?=$interfacename?> $innerProvider,
        private Closure $resultProcessor){
    }

    public function getClientID(): string {
        return $this->innerProvider->getClientID();
    }

    /**
     * Summary of handleResults
     * @template newSuccessT
     * @template newUnauthorizedT
     * @template newNotFoundT
     * @template newErrorT
     * @param Closure(TSuccessResult2|TErrorResult2|TNotFoundResult2|TUnauthorizedResult2) : (newSuccessT|newErrorT|newNotFoundT|newUnauthorizedT) $processor
     * @return <?=$interfacename?><newSuccessT,newErrorT,newNotFoundT,newUnauthorizedT>
     */
    public function handleResults(Closure $processor): <?=$interfacename?> {
        $previousProcessor = $this->resultProcessor ?? fn($x) => $x;
        return new <?=$wrappername?>( $this->innerProvider, fn($x) => $processor($previousProcessor($x)));
    }

    public function HandleEmitted(mixed $data, array $context): void {
        $this->innerProvider->HandleEmitted($data, $context);
    }

<?php
foreach($methods as $method){
    generateWrappedMethod($method);
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
function generateWrappedMethod(MethodDefinition $method){
    
    ?>
    /**
<?=$method->createDocstringParamsAndReturn(1)?>

     * @phpstan-ignore return.unresolvableType
    */
    public function <?=$method->name?>(<?=$method->paramString()?>) : mixed{
        $value = $this->innerProvider-><?=$method->name?>(<?=implode(', ', array_map(fn($x) => '$' . $x->name, $method->parameters))?>);
        return ($this->resultProcessor)($value);
    }

<?php
}