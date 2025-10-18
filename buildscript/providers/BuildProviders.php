<?php
namespace Buildscript\Providers;
include_once __DIR__ . '/ProviderOriginalParser.php';
use function Buildscript\Providers\processProviderFile;

function buildProviders($folder, $models){
    echo "Building providers in folder: $folder\n";
    $files = glob($folder . '/*.php');
    $names = array_map(fn($f) => [
        'file' => $f,
        'providername' => basename($f, "Provider.php"),
        'modelname' => basename($f, "Provider.php"),
        'traitname' => basename($f, ".php") . "Properties"
    ], $files);
    $mapped = array_map(fn($n) => processProviderFile($n['file'], $n['providername'], $n['traitname'], $n['modelname']), $names);



    //filter out __ methods, such as constructors.
    $mapped = array_map(function($item){
        $item['methods'] = array_filter($item['methods'], fn($x) => !str_starts_with($x['name'], '__'));
        return $item;
        }, $mapped);

    return $mapped;
}