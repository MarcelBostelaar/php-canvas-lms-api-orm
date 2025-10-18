<?php

namespace Buildscript;
use PhpParser\NodeTraverser;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;

class FindTraitUserVisitor extends AbstractExtractorVisitor{
    public $wasFound = false;
    public function __construct(public readonly string $traitname){}
    public function EnterNode(Node $node){
        if($node instanceof Stmt\TraitUse){
            $traits = $node->traits;
            foreach($traits as $trait){
                if($trait instanceof FullyQualified){
                    if($trait->name == $this->traitname || str_ends_with($trait->name, "\\" . $this->traitname)){
                        $this->wasFound = true;
                    }
                }
            }
        }
    }
}