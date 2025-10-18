<?php

namespace Buildscript;

class AbstractExtractorVisitor extends \PhpParser\NodeVisitorAbstract {
    public function process($ast){
        $traverser = new \PhpParser\NodeTraverser();
        $traverser->addVisitor(new \PhpParser\NodeVisitor\NameResolver());
        $traverser->addVisitor($this);
        $traverser->traverse($ast);
        return $this;
    }
}