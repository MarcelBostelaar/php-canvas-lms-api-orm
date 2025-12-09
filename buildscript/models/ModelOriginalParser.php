<?php

namespace Buildscript\Models;
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
 * @return array{ast: \PhpParser\Node[], fields: array{type: string, name: string}, fieldsNullable: array{type: string, name: string}, filename: string, plurals: string[]} \
 *   An array with the parsed AST, fields, nullable fields, filename and plurals.\
 *   ast: The parsed Abstract Syntax Tree of the PHP file.\
 *   fields: An array of fields with their fully qualified types, and names.\
 *   fieldsNullable: An array of nullable fields with their fully qualified types, and names.\
 *   filename: The name of the file being processed without the path.\
 *   plurals: An array of plural forms of the name of the model, for automatic method generation in providers. 
 */
function processModelFile($filePath, $modelname, $traitname){
    $ast = parseFile($filePath);
    $fields = (new FieldListFinderVisitor("properties"))->process($ast)->getList();
    $fieldsNullable = (new FieldListFinderVisitor("nullableProperties"))->process($ast)->getList();
    $plurals = (new PluralsFinderVisitor())->process($ast)->getPlurals();
    $traitFound = (new FindTraitUserVisitor($traitname))->process($ast)->wasFound;
    return ["ast" => $ast,
            "modelname" => $modelname,
            "traitname" => $traitname,
            "fields" => $fields,
            "fieldsNullable" => $fieldsNullable,
            "plurals" => $plurals,
            "hasTrait" => $traitFound
    ];
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
     * @return array
     */
    public function getList(){
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
            $foundItems[] = [
                "type" => $classname,
                "name" => $propertyName,
                "classType" => $classType
            ];
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
