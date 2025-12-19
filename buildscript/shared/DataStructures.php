<?php

namespace Buildscript;


abstract class TypeDefinitionBase {
    public function __construct(
        public bool $isArrayVariant = false,
    ) {}

    public abstract function __toString(): string;
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
        public readonly string $name,
        public readonly TypeDefinitionBase $type,
        public readonly TypeDefinitionBase $annotatedType,
        public readonly bool $isArrayVariant = false
    ) {}
}

/**
 * Represents a method return type with type information
 */
class MethodReturnType {
    public function __construct(
        public readonly TypeDefinitionBase $type,
        public readonly TypeDefinitionBase $annotatedType
    ) {}
}

/**
 * Represents a method definition with parameters and return type
 */
class MethodDefinition {
    /**
     * @param string $name
     * @param MethodParameter[] $parameters
     * @param MethodReturnType $returnType
     */
    public function __construct(
        public readonly string $name,
        public readonly array $parameters,
        public readonly MethodReturnType $returnType
    ) {}
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
 * Represents method information for code generation
 */
class MethodInfo {
    /**
     * @param string $methodPrefix
     * @param string $originalModelPlural
     * @param string $originalSubjectSingular
     * @param string $originalModelSingular
     * @param MethodParameter[] $parameters
     * @param MethodReturnType $returnType
     * @param self|null $original Reference to the original method for aliases
     */
    public function __construct(
        public readonly string $methodPrefix,
        public readonly string $originalModelPlural,
        public readonly string $originalSubjectSingular,
        public readonly string $originalModelSingular,
        public readonly array $parameters,
        public readonly MethodReturnType $returnType,
        public readonly ?self $original = null
    ) {}
    
    /**
     * Get the full method name
     */
    public function getMethodName(): string {
        return $this->methodPrefix . $this->originalModelPlural . 'In' . $this->originalSubjectSingular;
    }
}

/**
 * Represents information about populate methods to generate
 */
class PopulatorInfo {
    /**
     * @param string $modelname The model name
     * @param string[] $generatePopulateMethods The plural names to generate populate methods for
     */
    public function __construct(
        public readonly string $modelname,
        public readonly array $generatePopulateMethods
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
