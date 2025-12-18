<?php
namespace Buildscript\Providers;
include_once __DIR__ . '/ProviderOriginalParser.php';
use Exception;
use function Buildscript\Providers\processProviderFile;
use function Buildscript\varDumpNoAst;
use function Buildscript\prettified;
use function Buildscript\Providers\generateInterface;
use Buildscript\DataStructures\ModelParseResult;
use Buildscript\DataStructures\ProviderParseResult;

/**
 * @param string $folder
 * @param ModelParseResult[] $models
 * @return ProviderParseResult[]
 */
function buildProviders($folder, $models): array {
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
    $mapped = array_map(function(ProviderParseResult $item): ProviderParseResult {
        $filteredMethods = array_filter($item->methods, fn($x) => !str_starts_with($x->name, '__'));
        return new ProviderParseResult(
            $item->ast,
            $item->providername,
            $item->traitname,
            $item->hastrait,
            $item->modelname,
            array_values($filteredMethods)
        );
    }, $mapped);

    $pluralLookup = [];
    foreach($models as $model){
        $pluralLookup[$model->modelname] = $model->plurals;
    }

    foreach($mapped as $provider){
        $result = generateFullProviderTrait(
            $provider->traitname,
            $provider->methods,
            $pluralLookup
        );
        file_put_contents("$folder/Generated/Traits/" . $provider->traitname . '.php', prettified("<?php\n" . $result->trait));
        $generatedInterface = generateInterface($provider->providername . "ProviderInterface", array_merge($result->createdMethods, $provider->methods), $result->usedModels);
        file_put_contents("$folder/Generated/Interfaces/" . $provider->providername . "ProviderInterface" . '.php', prettified("<?php\n" . $generatedInterface));
        // echo "Provider: " . $provider->providername . "<br>";
        // echo "Original methods: \n";
        // var_dump($provider->methods);
        // echo "<br>Generated methods: \n";
        // var_dump($result->createdMethods);
        // echo "\n";
    }

    return $mapped;
}