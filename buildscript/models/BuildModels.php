<?php
namespace Buildscript\Models;

use PhpParser\NodeDumper;
include_once __DIR__ . '/ModelOriginalParser.php';
function buildModels($folder){
    // $testResuls = GenerateFullModelTrait(
    //     "TestTrait",
    //     [["name"=> "myNormalString", "type" => "string"], ["name"=> "myNormalInt", "type" => "int"]],
    //     [["name"=> "myNullableString", "type" => "string"], ["name"=> "myullableInt", "type" => "int"]],
    //     [["name"=> "mySection", "type" => "Section"], ["name"=> "myStudent", "type" => "Student"]],
    //     [["name"=> "myNullableCourse", "type" => "Course"], ["name"=> "myNullableSubmission", "type" => "Submission"]]
    // );

    //write to testfile.php
    // file_put_contents("testfile.php", $testResuls);
    
    // Get all PHP files in the folder (not recursively)
    $phpFiles = [];
    foreach (glob($folder . '/*.php') as $filePath) {
        $phpFiles[] = [$filePath];
    }
    $mapped = [];
    foreach ($phpFiles as $file) {
        if($file[0] === "Domain.php"){
            continue;//Domain.php is special, it does not have a trait
        }
        $filePath = $file[0];
        $parsedFile = processModelFile($filePath);
        $mapped[] = $parsedFile;
        //write to test folder
        $ast = $parsedFile["ast"];
        $name = $parsedFile["filename"];
        unset($parsedFile["ast"]);
        file_put_contents(__DIR__ . "/../test/" . $name, json_encode($parsedFile, JSON_PRETTY_PRINT) . (new NodeDumper)->dump($ast));
    }
    return  $mapped;
}

