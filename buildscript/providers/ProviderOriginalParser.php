<?php

namespace Buildscript\Providers;
use Buildscript\AbstractExtractorVisitor;
use Buildscript\AtomicTypeDefinition;
use Buildscript\UnionTypeDefinition;
use Buildscript\FindTraitUserVisitor;
use Exception;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\UnionType;
use function Buildscript\parseFile;
use Buildscript\MethodParameter;
use Buildscript\MethodReturnType;
use Buildscript\MethodDefinition;
use Buildscript\ProviderParseResult;
use function Buildscript\parseParamType;
use function Buildscript\parseReturnType;

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

        if($node->name->toString() === "__construct"){
            return null;
        }

        $docstring = $node->getDocComment();
        if(!$docstring){
            throw new Exception("No docstring found for method " . $node->name->toString());
        }

        //get name
        $name = $node->name->toString();
        //get params
        $params = [];
        foreach($node->params as $param){
            $docstringType = parseParamType($param->var->name, $docstring);
            $codeType = self::parseType($param->type);
            $stringified = (string)$codeType;
            $stringifiedDoc =(string)$docstringType;
            echo "Parsed param {$param->var->name} with code type {$stringified} and docstring type {$stringifiedDoc}\n";
            $params[] = new MethodParameter($param->var->name, $codeType, $docstringType);
        }
        $docstringReturnType = parseReturnType($docstring);
        $codeReturnType = self::parseType($node->getReturnType());
        $stringifiedReturn = (string)$codeReturnType;
        $stringifiedDocReturn = (string)$docstringReturnType;
        echo "Parsed return type for method $name with code type {$stringifiedReturn} and docstring type {$stringifiedDocReturn}\n";
        
        $this->methods[] = new MethodDefinition(
            $name,
            $params,
            new MethodReturnType($codeReturnType, $docstringReturnType)
        );
        return null;
    }

    private static function parseType(Identifier|Name|ComplexType $type){
        if($type instanceof \PhpParser\Node\Name\FullyQualified){
            return new AtomicTypeDefinition($type->toString());
        }
        if($type instanceof UnionType){
            return new UnionTypeDefinition(array_map(fn($x) => self::parseType($x), $type->types));
        }
        if($type instanceof Identifier){
            return new AtomicTypeDefinition($type->toString());
        }
        echo "Could not find a valid type for: \n";
        var_dump($type);
        throw new Exception();
    }
}