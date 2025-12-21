<?php
namespace Buildscript\Providers;
use Buildscript\MethodGenerationType;
use Exception;
use function Buildscript\Providers\processProviderFile;
use function Buildscript\varDumpNoAst;
use function Buildscript\prettified;
use function Buildscript\Providers\generateInterface;
use Buildscript\ModelParseResult;
use Buildscript\ProviderParseResult;

/**
 * @param string $folder
 * @param ModelParseResult[] $models
 * @return ProviderParseResult[]
 */
function buildProviders($folder, $models): array {
    echo "Building providers in folder: $folder\n";

    $pluralLookup = [];
    foreach($models as $model){
        $pluralLookup[$model->modelname] = $model->plurals;
    }
    $modelNames = array_keys($pluralLookup);

    $files = glob($folder . '/*Provider.php');
    $names = array_map(fn($f) => [
        'file' => $f,
        'providername' => basename($f, "Provider.php"),
        'modelname' => basename($f, "Provider.php"),
        'traitname' => basename($f, ".php") . "Properties"
    ], $files);
    $mapped = array_map(fn($n) => processProviderFile($n['file'], $n['providername'], $n['traitname'], $n['modelname'], $pluralLookup), $names);



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


    foreach($mapped as $provider){
        // var_dump($provider);
        $result = generateFullProviderTrait(
            $provider->traitname,
            array_filter($provider->methods, fn($x) => $x->generationType != MethodGenerationType::Other),
            $pluralLookup,
            $modelNames
        );
        file_put_contents("$folder/Generated/Traits/" . $provider->traitname . '.php', prettified("<?php\n" . $result->trait));
        $generatedInterface = generateInterface($provider->providername . "ProviderInterface", array_merge($result->createdMethods, $provider->methods), $modelNames);
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