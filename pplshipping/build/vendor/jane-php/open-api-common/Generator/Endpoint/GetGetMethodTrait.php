<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator\Endpoint;

use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
trait GetGetMethodTrait
{
    public function getGetMethod(OperationGuess $operation) : Stmt\ClassMethod
    {
        return new Stmt\ClassMethod('getMethod', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'stmts' => [new Stmt\Return_(new Scalar\String_($operation->getMethod()))], 'returnType' => new Name('string')]);
    }
}
