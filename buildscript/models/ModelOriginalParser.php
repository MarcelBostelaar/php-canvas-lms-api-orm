<?php

namespace Buildscript\Models;
use Buildscript\ModelParseResult;
use Buildscript\PropertyDefinition;
use Exception;
use function Buildscript\parseFile;
use Buildscript\AbstractExtractorVisitor;
use Buildscript\FindTraitUserVisitor;
use PhpParser\Node as n;
use PhpParser\Node\PropertyItem;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\NodeTraverser;
use PhpParser\Node;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeDumper;

/**
 * Takes a php file by path (a valid model) and processed it. Gives back ast and required data for further processing/generation.
 * @param mixed $filePath
 * @return ModelParseResult
 */
function processModelFile($filePath, $modelname, $traitname): ModelParseResult {
    $ast = parseFile($filePath);
    $fields = (new FieldListFinderVisitor("properties"))->process($ast)->getList();
    $fieldsNullable = (new FieldListFinderVisitor("nullableProperties"))->process($ast)->getList();
    $plurals = (new PluralsFinderVisitor())->process($ast)->getPlurals();
    $traitFound = (new FindTraitUserVisitor($traitname))->process($ast)->wasFound;
    $parentModel = new ParentClassVisitor()->process($ast)->parentClassName;
    
    return new ModelParseResult(
        $ast,
        $modelname,
        $traitname,
        $fields,
        $fieldsNullable,
        $plurals,
        $traitFound,
        $parentModel
    );
}


class FieldListFinderVisitor extends AbstractExtractorVisitor{
    public function __construct(private readonly string $varname){}
    public PropertyItem $found;
    public function enterNode(Node $node) {
        if($node instanceof PropertyItem){
            if($node->name instanceof VarLikeIdentifier){
                if($node->name->name == $this->varname){
                    $this->found = $node;
                }
            }
        }
    }

    /**
     * @return PropertyDefinition[]
     */
    public function getList(): array {
        $unexpectedNodeForm = fn() => new Exception("Found node not as expected");
        if(!isset($this->found)){
            echo "No $this->varname found.\n";
            return []; //Node not found, possible since it might only have normal or nullable properties.
        }
        $vals = $this->found->default;
        if(!($vals instanceof Array_)){
            throw $unexpectedNodeForm(); //must be list of arrays
        }
        $vals = $vals->items;
        $foundItems = [];
        foreach($vals as $item){
            if(!is_object($item)){
                throw $unexpectedNodeForm();
            }
            $value = $item->value;
            if(!$value instanceof Array_){
                throw $unexpectedNodeForm();
            }
            $key = $value->items[0]->value;
            $propnameContainer = $value->items[1]->value;
            if($key instanceof ClassConstFetch){
                if(!$key->class instanceof FullyQualified){
                    throw $unexpectedNodeForm(); //must be YourClass::class
                }
                $classname = $key->class->name;
                $classType = true;
            }
            elseif($key instanceof Scalar\String_){
                $classname = $key->value;
                $classType = false;
            }
            else{
                throw $unexpectedNodeForm(); //Name of class must be YourClass::class or a string
            }

            if(!$propnameContainer instanceof Scalar\String_){
                throw $unexpectedNodeForm(); //Name of prop must be of type string.
            }
            $propertyName = $propnameContainer->value;
            $foundItems[] = new PropertyDefinition($classname, $propertyName, $classType);
        }
        return $foundItems;
    }
}

class PluralsFinderVisitor extends AbstractExtractorVisitor{
    private PropertyItem $found;

    public function enterNode(Node $node) {
        if($node instanceof PropertyItem){
            if($node->name instanceof VarLikeIdentifier){
                if($node->name->name == "plurals"){
                    $this->found = $node;
                }
            }
        }
    }

    public function getPlurals(){
        $unexpectedNodeForm = fn() => new Exception("Found node not as expected");
        if(!isset($this->found)){
            throw $unexpectedNodeForm(); //No plurals found
        }
        if(!$this->found->default instanceof Array_){
            throw $unexpectedNodeForm();
        }
        $items = $this->found->default->items;
        $foundPlurals = [];
        foreach($items as $item){
            if(!is_object($item)){
                throw $unexpectedNodeForm();
            }
            $itemval = $item->value;
            if(!$itemval instanceof Scalar\String_){
                throw $unexpectedNodeForm();
            }
            $foundPlurals[] = $itemval->value;
        }
        return $foundPlurals;
    }
}

class ParentClassVisitor extends AbstractExtractorVisitor{
    public ?string $parentClassName = null;

    public function enterNode(Node $node) {
        if($node instanceof Stmt\Class_){
            if($node->extends instanceof FullyQualified){
                $data = $node->extends->toString();
                $exploded = explode("\\", $data);
                $this->parentClassName = $exploded[count($exploded) - 1];
            }
        }
    }
}