<?php
namespace Buildscript\Providers;
include_once __DIR__ . '/ProviderOriginalParser.php';
use Exception;
use function Buildscript\Providers\processProviderFile;
use function Buildscript\varDumpNoAst;
use function Buildscript\prettified;
use function Buildscript\Providers\generateInterface;

function buildProviders($folder, $models){
    echo "Building providers in folder: $folder\n";
    $files = glob($folder . '/*Provider.php');
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

    $pluralLookup = [];
    foreach($models as $model){
        $pluralLookup[$model['modelname']] = $model['plurals'];
    }

    foreach($mapped as $provider){
        [
            "trait" => $traitContent, 
            "createdMethods" => $createdMethods,
            "usedModels" => $usedModels
            ] = generateFullProviderTrait(
            $provider['traitname'],
            $provider['methods'],
            $pluralLookup
        );
        file_put_contents("$folder/Generated/Traits/" . $provider['traitname'] . '.php', prettified("<?php\n" . $traitContent));
        $generatedInterface = generateInterface($provider['providername'] . "ProviderInterface", array_merge($createdMethods, $provider['methods']), $usedModels);
        file_put_contents("$folder/Generated/Interfaces/" . $provider['providername'] . "ProviderInterface" . '.php', prettified("<?php\n" . $generatedInterface));
        // echo "Provider: " . $provider["providername"] . "<br>";
        // echo "Original methods: \n";
        // var_dump($provider["methods"]);
        // echo "<br>Generated methods: \n";
        // var_dump($createdMethods);
        // echo "\n";
    }

    return $mapped;
}