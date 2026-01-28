<?php

namespace Buildscript;

use Closure;


abstract class TypeDefinitionBase {
    public function __construct(
    ) {}

    public abstract function map(Closure $func);

    public abstract function codeString(): string;
    public abstract function annotatedString(): string;
}

class AtomicTypeDefinition extends TypeDefinitionBase {
    public function __construct(
        public string $type,
        public bool $isArrayVariant = false,
        public bool $isNullable = false
    ) {
        parent::__construct();
    }

    public function annotatedString(): string {

        return ($this->isNullable ? '?' : '') . $this->type . ($this->isArrayVariant ? '[]' : '') ;
    }

    public function codeString(): string {
        if($this->isArrayVariant){
            return 'array';
        }
        if($this->type === 'mixed'){
            return 'mixed';
        }
        return ($this->isNullable ? '?' : '') . $this->type;
    }

    public function map(Closure $func) {
        return $func($this);
    }

    public function mergeEnhance(TypeDefinitionBase $other): AtomicTypeDefinition {
        if(!($other instanceof AtomicTypeDefinition)){
            throw new \Exception("Cannot merge non-atomic type definition into atomic type definition");
        }
        if(!$this->isArrayVariant && $other->isArrayVariant){
            throw new \Exception("Cannot enhance atomic type definition to array variant if original is not array variant");
        }
        if(!$this->isNullable && $other->isNullable){
            throw new \Exception("Cannot enhance atomic type definition to nullable if original is not nullable");
        }
        //Assume other type is more specific
        return $other;
    }
}

class GenericTypeDefinition extends TypeDefinitionBase {
    /**
     * @param string $type
     * @param TypeDefinitionBase[] $genericParameters
     */
    public function __construct(
        public string $type,
        public array $genericParameters,
        public bool $isNullable = false,
        public bool $isArrayVariant = false
    ) {
        parent::__construct();
    }

    public function annotatedString(): string {
        $generics = implode(", ", array_map(fn($p) => $p->annotatedString(), $this->genericParameters));
        return ($this->isNullable ? '?' : '') . $this->type . "<$generics>" . ($this->isArrayVariant ? '[]' : '') ;
    }

    public function codeString(): string {
        if($this->isArrayVariant){
            return 'array';
        }
        return ($this->isNullable ? '?' : '') . $this->type;
    }

    public function map(Closure $func) {
        $mappedGenerics = array_map(fn($p) => $p->map($func), $this->genericParameters);
        $newInstance = new GenericTypeDefinition($this->type, $mappedGenerics, $this->isArrayVariant, $this->isNullable);
        return $func($newInstance);
    }
}

class UnionTypeDefinition extends TypeDefinitionBase {
    /**
     * @param TypeDefinitionBase[] $types
     */
    public function __construct(
        public array $types
    ) {
        parent::__construct();
    }

    public function annotatedString(): string {
        return implode("|", array_map(fn($p) => $p->annotatedString(), $this->types));
    }

    public function codeString(): string {
        return implode("|", array_map(fn($p) => $p->codeString(), $this->types));
    }

    public function map(Closure $func) {
        $mappedTypes = array_map(fn($p) => $p->map($func), $this->types);
        $newInstance = new UnionTypeDefinition($mappedTypes);
        return $func($newInstance);
    }
}



/**
 * Represents a property definition with its type and name
 */
class PropertyDefinition {
    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly bool $classType = false
    ) {}
}

/**
 * Represents a method parameter with type information
 */
class MethodParameter {
    public function __construct(
        public string $name,
        public TypeDefinitionBase $type
    ) {}

    public function paramString(): string {
        return $this->type->codeString() . ' $' . $this->name;
    }
}

enum MethodGenerationType {
    case PopulateSingle;
    case PopulateMultiple;
    case GetItemsInSingle;
    case GetItemsInMultiple;
    case InterfaceMethod;
    case Other;
}

/**
 * Represents a method definition with parameters and return type
 */
class MethodDefinition {

    public array $metadata = [];

    /**
     * Summary of __construct
     * @param string $name
     * @param string $docstring
     * @param MethodParameter[] $parameters
     * @param TypeDefinitionBase $returnType
     * @param MethodGenerationType $generationType
     * @param mixed $aliasOf
     * @param mixed $pluralVariantOf
     */
    public function __construct(
        public string $name,
        public string $docstring,
        public array $parameters,
        public TypeDefinitionBase $returnType,
        public MethodGenerationType $generationType,
        public ?MethodDefinition $aliasOf = null,
        public ?MethodDefinition $pluralVariantOf = null
    ) {}

    /**
     * Generates alias forms of the method based on plural variations
     * @param array{string:string[]} $pluralLookup
     * @return MethodDefinition[]
     */
    public function getAliasForms(array $pluralLookup): array{
        $aliases = [];

        foreach($pluralLookup as $pluralGroup){
            foreach($pluralGroup as $plural){
                $pos = strpos($this->name, $plural);
                if($pos === false){
                    continue;
                }

                foreach($pluralGroup as $aliasPlural){
                    if($aliasPlural === $plural){
                        continue;
                    }

                    $newName = str_replace($plural, $aliasPlural, $this->name);
                    if(isset($aliases[$newName])){
                        continue;
                    }

                    $lowerOriginal = lcfirst($plural);
                    $lowerAlias = lcfirst($aliasPlural);

                    $newParams = array_map(function(MethodParameter $param) use ($lowerOriginal, $lowerAlias){
                        $newParamName = str_replace($lowerOriginal, $lowerAlias, $param->name);
                        return new MethodParameter(
                            $newParamName,
                            $param->type
                        );
                    }, $this->parameters);

                    $item = new MethodDefinition(
                        $newName,
                        $this->docstring,
                        $newParams, 
                        $this->returnType, 
                        $this->generationType,
                        $this);
                    $aliases[$newName] = $item;
                }
            }
        }
        return array_values($aliases);
    }

    /**
     * @param array $pluralLookup
     * @return MethodDefinition[]
     */
    public function createPluralVariants(array $pluralLookup): array {
        if($this->generationType === MethodGenerationType::PopulateSingle){
            return $this->createPluralPopulateInternal($pluralLookup);
        }
        elseif($this->generationType === MethodGenerationType::GetItemsInSingle){
            return $this->createPluralGetItemsInternal($pluralLookup);
        }
        else{
            throw new \Exception("Method is not of type PopulateSingle or GetItemsInSingle: " . $this->name);
        }
    }

    /**
     * Summary of createPluralPopulateInternal
     * @param array $pluralLookup
     * @return MethodDefinition[]
     */
    private function createPluralPopulateInternal(array $pluralLookup): array {
        $definitions = [];
        if($this->generationType !== MethodGenerationType::PopulateSingle){
            throw new \Exception("Method is not of type PopulateSingle: " . $this->name);
        }
        foreach($pluralLookup as $singular => $plurals){

            $pos = strpos($this->name, $singular);
            if($pos === false){
                continue;
            }

            foreach($plurals as $plural){

                $newName = str_replace($singular, $plural, $this->name);

                $relevantParam = array_filter($this->parameters, fn($p) => $p->name == $this->metadata['relevantParam']);
                if(count($relevantParam) === 0){
                    echo "Could not find parameter {$this->metadata['relevantParam']} in method {$this->name} to create plural variant";
                    //Singular noun might be subset of plural noun, skip
                    continue;
                }
                $relevantParam = $relevantParam[0];

                $pluralParam = clone $relevantParam;
                $pluralParam->type = clone $relevantParam->type;
                $pluralParam->type->isArrayVariant = true;
                $pluralParam->name = lcfirst($plural); //take first plural as param name

                //replace relevant param with array variant
                $newparams = array_map(function(MethodParameter $param) use ($relevantParam, $pluralParam){
                    if($param->name == $relevantParam->name){
                        return $pluralParam;
                    }
                    return $param;
                }, $this->parameters);

                $newReturnType = $this->returnType->map(function($typeDef) {
                    if($typeDef instanceof GenericTypeDefinition){
                        if($typeDef->type === 'SuccessResult'){
                            $pluralSubtype = clone $typeDef->genericParameters[0];
                            $pluralSubtype->isArrayVariant = true;
                            return new GenericTypeDefinition(
                                'SuccessResult',
                                [
                                    $pluralSubtype
                                ]
                            );
                        }
                    }
                    return $typeDef;
                });

                $definitions[] = new MethodDefinition(
                    $newName,
                    $this->docstring,
                    $newparams,
                    $newReturnType,
                    MethodGenerationType::PopulateMultiple,
                    null,
                    $this
                );
            }
        }
        if(count($definitions) > 0){
            return $definitions;
        }
        throw new \Exception("Could not find singular form in method {$this->name} to create plural variant");
    }

    /**
     * Summary of createPluralGetItemsInternal
     * @param array{string:string[]} $pluralLookup
     * @return MethodDefinition[]
     */
    private function createPluralGetItemsInternal(array $pluralLookup): array {
        if($this->generationType !== MethodGenerationType::GetItemsInSingle){
            throw new \Exception("Method is not of type GetItemsForSingle: " . $this->name);
        }

        $split = preg_split("/In/", $this->name, 2);
        $head = $split[0];
        $tail = $split[1];

        $definitions = [];

        foreach($pluralLookup as $singular => $plurals){

            if($tail !== $singular){
                continue;
            }

            foreach($plurals as $plural){

                $newName = $head . "In" . $plural;

                $relevantParam = array_filter($this->parameters, fn($p) => $p->name == $this->metadata['relevantParam']);
                if(count($relevantParam) === 0){
                    echo "Could not find parameter {$this->metadata['relevantParam']} in method {$this->name} to create plural variant";
                    //Singular noun might be subset of plural noun, skip
                    continue;
                }
                $relevantParam = $relevantParam[0];

                $pluralParam = clone $relevantParam;
                $pluralParam->type = clone $relevantParam->type;
                $pluralParam->type->isArrayVariant = true;
                $pluralParam->name = lcfirst($plural); //take first plural as param name

                //replace relevant param with array variant
                $newparams = array_map(function(MethodParameter $param) use ($relevantParam, $pluralParam){
                    if($param->name == $relevantParam->name){
                        return $pluralParam;
                    }
                    return $param;
                }, $this->parameters);

                //Return type becomes lookup of model
                $newAnnotatedReturnType = $this->returnType
                    ->map(function($typeDef) use ($relevantParam) {
                    if($typeDef instanceof GenericTypeDefinition){
                        if($typeDef->type === 'SuccessResult'){

                            $pluralSubtype = clone $typeDef->genericParameters[0];
                            $pluralSubtype->isArrayVariant = false;

                            return new GenericTypeDefinition(
                                'SuccessResult',
                                [
                                    new GenericTypeDefinition(
                                        'Lookup',
                                        [
                                            $relevantParam->type,
                                            $pluralSubtype
                                        ]
                                    )
                                ]
                            );
                        }
                    }
                    return $typeDef;
                });

                $newDef = new MethodDefinition(
                    $newName,
                    $this->docstring,
                    $newparams,
                    $newAnnotatedReturnType,
                    MethodGenerationType::GetItemsInMultiple,
                    null,
                    $this
                );
                $newDef->metadata['relevantParam'] = $pluralParam->name;
                $definitions[] = $newDef;
            }
        }
        return $definitions;
    }

    public function createDocstringParamsAndReturn($tabDepth = 0): string {
        $lines = array_map(function(MethodParameter $param){
            $annotatedType = $param->type->annotatedString();
            return " * @param {$annotatedType} \${$param->name}";
        }, $this->parameters);
        $annotatedType = $this->returnType->annotatedString();
        $lines[] = " * @return {$annotatedType}";
        $paramLines = array_map(fn($line) => str_repeat("\t", $tabDepth) . $line, $lines);
        return implode("\n", $paramLines);
    }

    public function paramString(): string {
        $total = [];
        foreach($this->parameters as $param){
            if($param->name === "skipCache"){
                $total[] = 'bool $skipCache = false';
            }
            elseif($param->name === "doNotCache"){
                $total[] = 'bool $doNotCache = false';
            }
            else{
                $total[] = $param->paramString();
            }
        }
        return implode(', ', $total);
    }
}



/**
 * Represents the parsed result of a model file
 */
class ModelParseResult {
    /**
     * @param array $ast The parsed Abstract Syntax Tree
     * @param string $modelname The name of the model
     * @param string $traitname The name of the generated trait
     * @param PropertyDefinition[] $fields Regular fields
     * @param PropertyDefinition[] $fieldsNullable Nullable fields
     * @param string[] $plurals Plural forms of the model name
     * @param bool $hasTrait Whether the trait is used
     * @param string|null $parentModel The name of the parent model, if any
     */
    public function __construct(
        public readonly array $ast,
        public readonly string $modelname,
        public readonly string $traitname,
        public readonly array $fields,
        public readonly array $fieldsNullable,
        public readonly array $plurals,
        public readonly bool $hasTrait,
        public readonly ?string $parentModel,
        public readonly ?string $generatedTrait = null
    ) {}
    
    /**
     * Create a new instance with the generated trait code
     */
    public function withGeneratedTrait(string $generatedTrait): self {
        return new self(
            $this->ast,
            $this->modelname,
            $this->traitname,
            $this->fields,
            $this->fieldsNullable,
            $this->plurals,
            $this->hasTrait,
            $this->parentModel,
            $generatedTrait
        );
    }
}

/**
 * Represents the parsed result of a provider file
 */
class ProviderParseResult {
    /**
     * @param array $ast The parsed Abstract Syntax Tree
     * @param string $providername The name of the provider
     * @param string $traitname The name of the generated trait
     * @param bool $hastrait Whether the trait is used
     * @param string $modelname The name of the associated model
     * @param MethodDefinition[] $methods The methods found in the provider
     */
    public function __construct(
        public readonly array $ast,
        public readonly string $providername,
        public readonly string $traitname,
        public readonly bool $hastrait,
        public readonly string $modelname,
        public readonly array $methods
    ) {}
}

/**
 * Represents the result of generating a provider trait
 */
class ProviderTraitResult {
    /**
     * @param string $trait The generated trait code
     * @param MethodDefinition[] $createdMethods The methods that were created
     * @param string[] $usedModels The models used in the trait
     */
    public function __construct(
        public readonly string $trait,
        public readonly array $createdMethods,
        public readonly array $usedModels
    ) {}
}
