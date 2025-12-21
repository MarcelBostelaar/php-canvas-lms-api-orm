<?php

namespace Buildscript;
require_once __DIR__ . '/vendor/autoload.php';
use function Buildscript\Models\buildModels;
use function Buildscript\Providers\buildProviders;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

$srcFolder = __DIR__ . '/../src/Core';

function build(){
    global $srcFolder;
    $modelTraitFolder = $srcFolder . "/Models/Generated";
    $providerTraitFolder = $srcFolder . "/Providers/Generated/Traits";
    $providerInterfaceFolder = $srcFolder . "/Providers/Generated/Interfaces";
    $providerWrapperFolder = $srcFolder . "/Providers/Generated/Wrappers";
    clearFolder(__DIR__ . "/test");
    clearFolder($modelTraitFolder);
    clearFolder($providerTraitFolder);
    clearFolder($providerInterfaceFolder);
    clearFolder($providerWrapperFolder);
    $models = buildModels($srcFolder . "/Models", $modelTraitFolder);
    $providers = buildProviders($srcFolder . "/Providers", $models); //provide category for testing
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