<?php

namespace Buildscript;

use Closure;


abstract class TypeDefinitionBase {
    public function __construct(
        public bool $isArrayVariant = false,
    ) {}

    public abstract function __toString(): string;

    public abstract function map(Closure $func);
}

class AtomicTypeDefinition extends TypeDefinitionBase {
    public function __construct(
        public string $type,
        bool $isArrayVariant = false,
        public bool $isNullable = false
    ) {
        parent::__construct($isArrayVariant);
    }

    public function __toString(): string {
        return ($this->isNullable ? '?' : '') . $this->type . ($this->isArrayVariant ? '[]' : '') ;
    }

    public function map(Closure $func) {
        return $func($this);
    }
}

class GenericTypeDefinition extends TypeDefinitionBase {
    /**
     * @param string $type
     * @param TypeDefinitionBase[] $genericParameters
     * @param bool $isArrayVariant
     */
    public function __construct(
        public string $type,
        public array $genericParameters,
        bool $isArrayVariant = false,
        public bool $isNullable = false
    ) {
        parent::__construct($isArrayVariant);
    }

    public function __toString(): string {
        $generics = implode(", ", array_map(fn($p) => (string)$p, $this->genericParameters));
        return ($this->isNullable ? '?' : '') . $this->type . "<$generics>" . ($this->isArrayVariant ? '[]' : '') ;
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
     * @param bool $isArrayVariant
     */
    public function __construct(
        public array $types
    ) {
        parent::__construct();
    }

    public function __toString(): string {
        return implode("|", array_map(fn($p) => (string)$p, $this->types));
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
        public TypeDefinitionBase $type,
        public TypeDefinitionBase $annotatedType
    ) {}

    public function paramString(): string {
        return $this->type->__toString() . ' $' . $this->name;
    }
}

/**
 * Represents a method return type with type information
 */
class MethodReturnType {
    public function __construct(
        public TypeDefinitionBase $type,
        public TypeDefinitionBase $annotatedType
    ) {}
}

enum MethodGenerationType {
    case PopulateSingle;
    case PopulateMultiple;
    case GetItemsForSingle;
    case GetItemsForMultiple;
    case InterfaceMethod;
    case Other;
}

/**
 * Represents a method definition with parameters and return type
 */
class MethodDefinition {

    public function __construct(
        public string $name,
        public string $docstring,
        public array $parameters,
        public MethodReturnType $returnType,
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
                            $param->type,
                            $param->annotatedType
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
        $onePlural = $this->createPluralVariantInternal($pluralLookup);
        $aliases = $onePlural->getAliasForms($pluralLookup);
        return array_merge([$onePlural], $aliases);
    }

    /**
     * Summary of createPluralVariant
     * @param array{string:string[]} $pluralLookup
     * @return MethodDefinition
     */
    private function createPluralVariantInternal(array $pluralLookup): MethodDefinition {
        foreach($pluralLookup as $singular => $plurals){

            $pos = strpos($this->name, $singular);
            if($pos === false){
                continue;
            }

            foreach($plurals as $plural){

                $newName = str_replace($singular, $plural, $this->name);

                $lowerOriginal = lcfirst($singular);
                $relevantParam = array_filter($this->parameters, fn($p) => $p->name == $lowerOriginal);
                if(count($relevantParam) === 0){
                    echo "Could not find parameter $lowerOriginal in method {$this->name} to create plural variant";
                    //Singular noun might be subset of plural noun, skip
                    continue;
                }
                $relevantParam = $relevantParam[0];

                $pluralParam = clone $relevantParam;
                $pluralParam->type = clone $relevantParam->type;
                $pluralParam->type->isArrayVariant = true;
                $pluralParam->annotatedType = clone $relevantParam->annotatedType;
                $pluralParam->annotatedType->isArrayVariant = true;

                //replace relevant param with array variant
                $newparams = array_map(function(MethodParameter $param) use ($relevantParam, $pluralParam){
                    if($param->name == $relevantParam->name){
                        return $pluralParam;
                    }
                    return $param;
                }, $this->parameters);

                if($this->generationType === MethodGenerationType::PopulateSingle){
                    //Same in as out, simple map is fine

                    $newReturnType = clone $this->returnType;
                    $newReturnType->isArrayVariant = true;
                    return new MethodDefinition(
                        $newName,
                        $this->docstring,
                        $newparams,
                        $newReturnType,
                        MethodGenerationType::PopulateMultiple,
                        null,
                        $this
                    );
                }
                
                //GetItemsForSingle
                if($this->generationType === MethodGenerationType::GetItemsForSingle){
                    //Return type becomes lookup of model
                    $newAnnotatedReturnType = $this->returnType->annotatedType
                        ->map(function($typeDef) use ($relevantParam) {
                        if($typeDef instanceof GenericTypeDefinition){
                            if($typeDef->type === 'SuccessResult'){

                                $pluralSubtype = clone $typeDef->genericParameters[0];
                                $pluralSubtype->isArrayVariant = true;

                                return new GenericTypeDefinition(
                                    'SuccessResult',
                                    [
                                        new GenericTypeDefinition(
                                            'Lookup',
                                            [
                                                $relevantParam->annotatedType,
                                                $pluralSubtype
                                            ]
                                        )
                                    ]
                                );
                            }
                        }
                        return $typeDef;
                    });
                    return new MethodDefinition(
                        $newName,
                        $this->docstring,
                        $newparams,
                        new MethodReturnType(
                            $this->returnType->type,
                            $newAnnotatedReturnType
                        ),
                        MethodGenerationType::GetItemsForMultiple,
                        null,
                        $this
                    );
                }
                throw new \Exception("Cannot create plural variant for method {$this->name} with generation type {$this->generationType->name}");
            }
        }
        throw new \Exception("Could not find singular form in method {$this->name} to create plural variant");
    }

    public function createDocstringParamsAndReturn($tabDepth = 0): string {
        $lines = array_map(function(MethodParameter $param){
            return " * @param {$param->annotatedType} \${$param->name}";
        }, $this->parameters);
        $lines[] = " * @return {$this->returnType->annotatedType}";
        $paramLines = array_map(fn($line) => str_repeat("\t", $tabDepth) . $line, $lines);
        return implode("\n", $paramLines);
    }

    public function paramString(): string {
        return implode(', ', array_map(fn(MethodParameter $p) => $p->paramString(), $this->parameters));
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
     * @param string|null $generatedTrait The generated trait code
     */
    public function __construct(
        public readonly array $ast,
        public readonly string $modelname,
        public readonly string $traitname,
        public readonly array $fields,
        public readonly array $fieldsNullable,
        public readonly array $plurals,
        public readonly bool $hasTrait,
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
