<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator\Endpoint;

use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
trait GetAuthenticationScopesTrait
{
    public function getAuthenticationScopesMethod(OperationGuess $operation) : Stmt\ClassMethod
    {
        $securityScopes = [];
        foreach ($operation->getSecurityScopes() as $scope) {
            $securityScopes[] = new Expr\ArrayItem(new Scalar\String_($scope));
        }
        return new Stmt\ClassMethod('getAuthenticationScopes', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'returnType' => new Name('array'), 'stmts' => [new Stmt\Return_(new Expr\Array_($securityScopes))]]);
    }
}
