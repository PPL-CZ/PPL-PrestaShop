<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator\Authentication;

use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Stmt;
trait ClassGenerator
{
    protected function createClass(string $name, array $statements) : Stmt\Class_
    {
        return new Stmt\Class_($name, ['stmts' => $statements, 'implements' => [new Name\FullyQualified('PPLShipping\\Jane\\Component\\OpenApiRuntime\\Client\\AuthenticationPlugin')]]);
    }
}
