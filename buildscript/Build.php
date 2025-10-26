<?php

namespace Buildscript;
require_once __DIR__ . '/vendor/autoload.php';
use function Buildscript\Models\buildModels;
use function Buildscript\Providers\buildProviders;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

$srcFolder = __DIR__ . '/../src';

function build(){
    global $srcFolder;
    $modelTraitFolder = $srcFolder . "/models/generated";
    $providerTraitFolder = $srcFolder . "/providers/generated";
    clearFolder(__DIR__ . "/test");
    clearFolder($modelTraitFolder);
    clearFolder($providerTraitFolder);
    $models = buildModels($srcFolder . "/models", $modelTraitFolder);
    $providers = buildProviders($srcFolder . "/providers", $models); //provide category for testing
}

function clearFolder($folder){
    //if folder does not exist, create it
    if(!is_dir($folder)){
        mkdir($folder);
        return;
    }

    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
}



build();
echo "Build complete\n";