<?php

namespace Buildscript\Providers;
use Buildscript\AbstractExtractorVisitor;
use Buildscript\FindTraitUserVisitor;
use function Buildscript\parseFile;

function processProviderFile($filePath, $providername, $traitname, $modelname){
    $ast = parseFile($filePath);

    // dump to test/providername-ast.json
    $dumper = new \PhpParser\NodeDumper(['dumpComments' => true]);
    $dump = $dumper->dump($ast);

    $outFile = "./test/$providername-ast.txt";


    $traitFound = (new FindTraitUserVisitor($traitname))->process($ast)->wasFound;
    $methods = (new ExtractProviderMethodsVisitor())->process($ast)->methods;

    $extraData = json_encode([
            "providername" => $providername,
            "traitname" => $traitname,
            "hasTrait" => $traitFound,
            "methods" => $methods], JSON_PRETTY_PRINT);
    
    file_put_contents($outFile, $extraData . "\n" . $dump);

    return ["ast" => $ast,
            "providername" => $providername,
            "traitname" => $traitname,
            "hasTrait" => $traitFound,
            "modelName" => $modelname,
            "methods" => $methods
    ];
}

class ExtractProviderMethodsVisitor extends AbstractExtractorVisitor {
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
            $params[] = ["name" => $param->var->name, "type" => $paramType];
        }
        //get return type
        $returnType = $node->getReturnType() instanceof \PhpParser\Node\NullableType ? "?" . $node->getReturnType()->type->toString() : ($node->getReturnType() instanceof \PhpParser\Node\Name ? $node->getReturnType()->toString() : (is_string($node->getReturnType()) ? $node->getReturnType() : "mixed"));
        $this->methods[] = ["name" => $name, "params" => $params, "returnType" => $returnType];
        return null;
    }
}