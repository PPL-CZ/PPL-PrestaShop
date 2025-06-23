<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator\Authentication;

use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\SecuritySchemeGuess;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
trait GetScopeGenerator
{
    protected function createGetScope(SecuritySchemeGuess $securityScheme) : Stmt\ClassMethod
    {
        return new Stmt\ClassMethod('getScope', ['returnType' => new Name('string'), 'stmts' => [new Stmt\Return_(new Scalar\String_($securityScheme->getName()))], 'type' => Stmt\Class_::MODIFIER_PUBLIC]);
    }
}
