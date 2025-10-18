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
