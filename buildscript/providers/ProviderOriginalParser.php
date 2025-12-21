<?php

namespace Buildscript\Providers;
use Buildscript\AbstractExtractorVisitor;
use Buildscript\AtomicTypeDefinition;
use Buildscript\GenericTypeDefinition;
use Buildscript\MethodGenerationType;
use Buildscript\TypeDefinitionBase;
use Buildscript\UnionTypeDefinition;
use Buildscript\FindTraitUserVisitor;
use Exception;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;
use function Buildscript\parseFile;
use Buildscript\MethodParameter;
use Buildscript\MethodReturnType;
use Buildscript\MethodDefinition;
use Buildscript\ProviderParseResult;
use function Buildscript\parseParamType;
use function Buildscript\parseReturnType;

/**
 * @param string $filePath
 * @param string $providername
 * @param string $traitname
 * @param string $modelname
 * @param array{string: string[]} $pluralLookup
 * @return ProviderParseResult
 */
function processProviderFile(string $filePath, string $providername, string $traitname, string $modelname, array $pluralLookup): ProviderParseResult {
    $ast = parseFile($filePath);
    $traitFound = (new FindTraitUserVisitor($traitname))->process($ast)->wasFound;
    $methods = (new ExtractProviderMethodsVisitor($pluralLookup))->process($ast)->methods;

    return new ProviderParseResult(
        $ast,
        $providername,
        $traitname,
        $traitFound,
        $modelname,
        $methods
    );
}

class ExtractProviderMethodsVisitor extends AbstractExtractorVisitor {
    /** @var MethodDefinition[] */
    public $methods = [];

    /**
     * Summary of __construct
     * @param array{string: string[]} $pluralLookup
     */
    public function __construct(private array $pluralLookup) {
    }

    public function enterNode(\PhpParser\Node $node) {
        if (!$node instanceof \PhpParser\Node\Stmt\ClassMethod) {
            return null;
        }
        if(!$node->isPublic() || $node->isStatic()){
            return null;
        }

        if($node->name->toString() === "__construct"){
            return null;
        }

        $docstring = $node->getDocComment();
        if(!$docstring){
            throw new Exception("No docstring found for method " . $node->name->toString());
        }

        $docstringText = $docstring->getText();

        //get name
        $name = $node->name->toString();
        //get params
        $params = [];
        foreach($node->params as $param){
            $typeInCode = self::parseType($param->type);
            $docstringType = parseParamType($param->var->name, $docstring, $typeInCode);
            $stringifiedDoc = $docstringType->annotatedString();
            echo "Parsed param {$param->var->name} with type {$stringifiedDoc}\n";
            $params[] = new MethodParameter($param->var->name, $docstringType);
        }
        $docstringReturnType = parseReturnType($docstring);
        $stringifiedDocReturn = $docstringReturnType->annotatedString();
        echo "Parsed return type for method $name with docstring type {$stringifiedDocReturn}\n";
        
        $method = new MethodDefinition(
            $name,
            $docstringText,
            $params,
            $docstringReturnType,
            MethodGenerationType::Other
        );
        $this->methods[] = $method;
        //Try to specify generation type
        if(self::tryCheckIsMethodPopulator($method)){
            return null;
        }
        if($this->tryCheckIsXInYMethod($method)){
            return null;
        }
        //No special type found
        return null;
    }

    /**
     * Checks if this method is a populator, sets its type if it is.
     * Method is considered a populator if it has a param, and the return is a union type containing SuccessResult of that param type
     * check if union type of success, unauthorized, notfound, error
     * @param MethodDefinition $method
     * @return bool
     */
    private static function tryCheckIsMethodPopulator(MethodDefinition $method): bool {
        $wrappedType = self::extractUnionWrappedType($method->returnType);
        if($wrappedType === null){
            return false;
        }
        //try find it in params
        foreach($method->parameters as $param){
            if($param->type->annotatedString() == $wrappedType->annotatedString()){
                $method->metadata['relevantParam'] = $param->name;
                $method->generationType = MethodGenerationType::PopulateSingle;
                return true;
            }
        }
        return false;
    }

    public static function extractUnionWrappedType(TypeDefinitionBase $type): ?TypeDefinitionBase {
        if(!($type instanceof UnionTypeDefinition)){
            return null;
        }
        $foundType = null;
        $notFoundResult = false;
        $unauthorizedResult = false;
        $errorResult = false;
        foreach($type->types as $subtype){
            if($subtype instanceof GenericTypeDefinition && $subtype->type === "SuccessResult"){
                if(count($subtype->genericParameters) !== 1){
                    return null;
                }
                $foundType = $subtype->genericParameters[0];
                continue;
            }
            else if(!$subtype instanceof AtomicTypeDefinition){
                return null;
            }
            switch($subtype->type){
                case "UnauthorizedResult":
                    $unauthorizedResult = true;
                    break;
                case "NotFoundResult":
                    $notFoundResult = true;
                    break;
                case "ErrorResult":
                    $errorResult = true;
                    break;
                default:
                    return null;
            };
        }
        if($foundType !== null && $notFoundResult && $unauthorizedResult && $errorResult){
            return $foundType;
        }
        return null;
    }

    /**
     * Checks if item is an XInY method, sets type if so.
     * @param MethodDefinition $method
     * @throws Exception
     * @return bool
     */
    private function tryCheckIsXInYMethod(MethodDefinition $method): bool {
        //split name on "In"
        $parts = preg_split('/In/', $method->name);
        if(count($parts) !== 2){
            //Not an In method
            return false;
        }
        $returnWrappedType = self::extractUnionWrappedType($method->returnType);
        if($returnWrappedType === null){
            //return type is wrong.
            return false;
        }
        $head = $parts[0];
        $tail = $parts[1];
        $foundMatchingPlural = false;
        $foundMatchingSingular = false;

        foreach($this->pluralLookup as $singular => $plurals){
            if($tail === $singular){
                //try finding param with type matching singular
                foreach($method->parameters as $param){
                    if($param->type->annotatedString() === $singular){
                        if($foundMatchingSingular){
                            throw new Exception("Method " . $method->name . " matches multiple singular forms for parameter");
                        }
                        $method->metadata['relevantParam'] = $param->name;
                        $foundMatchingSingular = true;
                    }
                }
            }
            foreach($plurals as $plural){
                if(str_ends_with($head, $plural)){
                    if($returnWrappedType->annotatedString() === $singular){
                        if($foundMatchingPlural){
                            throw new Exception("Method " . $method->name . " matches multiple plural forms for return type");
                        }
                        $foundMatchingPlural = true;
                    }
                }
            }
        }
        if($foundMatchingPlural && $foundMatchingSingular){
            $method->generationType = MethodGenerationType::GetItemsInSingle;
            return true;
        }
        return false;
    }

    private static function parseType(Identifier|Name|ComplexType $type){
        if($type instanceof \PhpParser\Node\Name\FullyQualified){
            return new AtomicTypeDefinition($type->toString());
        }
        if($type instanceof UnionType){
            return new UnionTypeDefinition(array_map(fn($x) => self::parseType($x), $type->types));
        }
        if($type instanceof Identifier){
            return new AtomicTypeDefinition($type->toString());
        }
        if($type instanceof NullableType){
            $subtype = self::parseType($type->type);
            $subtype->isNullable = true;
            return $subtype;
        }
        echo "Could not find a valid type for: \n";
        var_dump($type);
        throw new Exception();
    }
}