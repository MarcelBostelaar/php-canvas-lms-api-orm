<?php

namespace Buildscript;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;

function parseFile($filePath){
    
    // Create parser
    $parser = (new ParserFactory)->createForNewestSupportedVersion();
    
    echo "Processing: $filePath\n";
    
    // Read the file
    $code = file_get_contents($filePath);
    
    // Parse the code
    $ast = $parser->parse($code);
    
    // Optional: Add name resolution (resolves class names, etc.)
    $traverser = new NodeTraverser();
    $traverser->addVisitor(new NameResolver());
    $ast = $traverser->traverse($ast);
    return $ast;
}

function tryExtractModelClassName($typeString){
    if(preg_match('/CanvasApiLibrary\\\\Core\\\\Models\\\\([A-Z][a-zA-Z0-9_]*)$/', $typeString, $matches)){
        return $matches[1];
    }
    return null;
}

function varDumpNoAst($data){
    if(is_array($data)){
        if(isset($data['ast'])){
            unset($data['ast']);
        }
        var_dump($data);
    } else {
        var_dump($data);
    }
}

function prettified(string $code): string {
    //temp disable
    return $code;
    // $phpParser = (new ParserFactory())->createForNewestSupportedVersion();
    // $ast = $phpParser->parse($code);

    // $prettyPrinter = new \PhpParser\PrettyPrinter\Standard();
    // return $prettyPrinter->prettyPrintFile($ast);
}