<?php

namespace Buildscript;
require_once __DIR__ . '/vendor/autoload.php';
use function Buildscript\Models\buildModels;
use function Buildscript\Providers\buildProviders;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

$buildFolder = __DIR__ . '/../build';
$srcFolder = __DIR__ . '/../src';

function build(){
    global $buildFolder;
    copyEverything();
    buildModels($buildFolder . "/models");
    buildProviders($buildFolder . "/providers");
}

function copyEverything(){
    clearBuildFolder();
    echo "Copying everything from src to build\n";
    global $buildFolder, $srcFolder;
    copyRecursive($srcFolder, $buildFolder);
}

function copyRecursive($src, $dst){
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                copyRecursive($src . '/' . $file,$dst . '/' . $file);
            }
            else { 
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function clearBuildFolder(){
    echo "Clearing build folder\n";
    global $buildFolder;
    //delete including subfolders
    if (!is_dir($buildFolder)) {
        return;
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($buildFolder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
}

build();
echo "Build complete\n";