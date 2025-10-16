<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeDumper;

function buildModels($folder){
    
    // Get all PHP files in the folder (not recursively)
    $phpFiles = [];
    foreach (glob($folder . '/*.php') as $filePath) {
        $phpFiles[] = [$filePath];
    }
    
    foreach ($phpFiles as $file) {
        $filePath = $file[0];
        $ast = parseFile($filePath);
        $dumper = new NodeDumper;
        echo $dumper->dump($ast) . "\n"; 

        //debug
        return;
    }
}

function parseFile($filePath){
    
    // Create parser
    $parser = (new ParserFactory)->createForNewestSupportedVersion();
    
    echo "Processing: $filePath\n";
    echo str_repeat("=", 80) . "\n";
    
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