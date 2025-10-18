<?php

// use PhpParser\Node\Expr\Array_;
// use PhpParser\Node\Expr\ClassConstFetch;
// use PhpParser\Node\Name\FullyQualified;
// use PhpParser\Node\Scalar as Scalar;
// use PhpParser\Node;

// class DR{
//     public function __construct(public readonly $value, public readonly $stack)
// }

// class NodeDeconstructor{
//     private $errors = [];

//     private function processIs(Node $node, string $type){
//         if(is_a($node, $type)){
//             return $node;
//         }
//         return "$node is not a $type";
//     }

//     public function is(string $type){

//     }
// }

// function ND(){
//     return new NodeDeconstructor();
// }

// $test = ND();
// $test->is(Array_::class)->itemsForeach(
//     ND()->value(
//         ND()->is(Array_::class)
//         ->itemsPositional(
//             //0: Class type
//             ND()->value(
//                 ND()->match(
//                     //MyClass::class
//                     ND()->is(ClassConstFetch::class)->class()->is(FullyQualified::class)->name()->callback($setClassName)->callback(fn($_) => $classType = true),
//                     //string
//                     ND()->is(Scalar\String_::class)->value()->callback($setClassName)->callback(fn($_) => $classType = false),
//                 )
//             ),
//             //1: Property name
//             ND()->value()->is(Scalar\String_::class)->value()->callback($setPropertyName)
//         )
//     )
// );