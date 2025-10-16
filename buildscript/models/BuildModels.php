<?php
namespace Buildscript\Models;

use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeDumper;

function buildModels($folder){
    $testResuls = GenerateFullModelTrait(
        "TestTrait",
        [["name"=> "myNormalString", "type" => "string"], ["name"=> "myNormalInt", "type" => "int"]],
        [["name"=> "myNullableString", "type" => "string"], ["name"=> "myullableInt", "type" => "int"]],
        [["name"=> "mySection", "type" => "Section"], ["name"=> "myStudent", "type" => "Student"]],
        [["name"=> "myNullableCourse", "type" => "Course"], ["name"=> "myNullableSubmission", "type" => "Submission"]]
    );

    //write to testfile.php
    file_put_contents("testfile.php", $testResuls);
    
    // Get all PHP files in the folder (not recursively)
    $phpFiles = [];
    foreach (glob($folder . '/*.php') as $filePath) {
        $phpFiles[] = [$filePath];
    }
    
    foreach ($phpFiles as $file) {
        if($file[0] === "Domain.php"){
            continue;//Domain.php is special, it does not have a trait
        }
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