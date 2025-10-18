<?php
namespace Buildscript\Providers;
include_once __DIR__ . '/ProviderOriginalParser.php';
use Exception;
use function Buildscript\Providers\processProviderFile;
use function Buildscript\varDumpNoAst;

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

    $pluralLookup = [];
    foreach($models as $model){
        $pluralLookup[$model['modelname']] = $model['plurals'];
    }

    foreach($mapped as $provider){
        $traitContent = generateFullProviderTrait(
            $provider['traitname'],
            $provider['modelname'],
            $pluralLookup[$provider['modelname']],
            $provider['methods'],
            $pluralLookup
        );
        file_put_contents("$folder/generated/" . $provider['traitname'] . '.php', "<?php\n" . $traitContent);
    }

    return $mapped;
}