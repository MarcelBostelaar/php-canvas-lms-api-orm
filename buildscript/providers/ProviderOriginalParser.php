<?php

namespace Buildscript\Providers;
use Buildscript\AbstractExtractorVisitor;
use Buildscript\FindTraitUserVisitor;
use function Buildscript\parseFile;
use Buildscript\DataStructures\MethodParameter;
use Buildscript\DataStructures\MethodReturnType;
use Buildscript\DataStructures\MethodDefinition;
use Buildscript\DataStructures\ProviderParseResult;

function processProviderFile($filePath, $providername, $traitname, $modelname): ProviderParseResult {
    $ast = parseFile($filePath);
    $traitFound = (new FindTraitUserVisitor($traitname))->process($ast)->wasFound;
    $methods = (new ExtractProviderMethodsVisitor())->process($ast)->methods;

    return new ProviderParseResult(
        $ast,
        $providername,
        $traitname,
        $traitFound,
        $modelname,
        $methods
    );
}

class ExtractProviderMethodsVisitor extends AbstractExtractorVisitor {
    /** @var MethodDefinition[] */
    public $methods = [];

    public function enterNode(\PhpParser\Node $node) {
        if (!$node instanceof \PhpParser\Node\Stmt\ClassMethod) {
            return null;
        }
        if(!$node->isPublic() || $node->isStatic()){
            return null;
        }

        //get name
        $name = $node->name->toString();
        //get params
        $params = [];
        foreach($node->params as $param){
            $paramType = $param->type instanceof \PhpParser\Node\NullableType ? "?" . $param->type->type->toString() : ($param->type instanceof \PhpParser\Node\Name ? $param->type->toString() : (is_string($param->type) ? $param->type : "mixed"));
            $params[] = new MethodParameter($param->var->name, $paramType, $paramType);
        }
        //get return type
        $returnType = $node->getReturnType() instanceof \PhpParser\Node\NullableType ? "?" . $node->getReturnType()->type->toString() : ($node->getReturnType() instanceof \PhpParser\Node\Name ? $node->getReturnType()->toString() : (is_string($node->getReturnType()) ? $node->getReturnType() : "mixed"));
        $this->methods[] = new MethodDefinition(
            $name,
            $params,
            new MethodReturnType($returnType, $returnType)
        );
        return null;
    }
}