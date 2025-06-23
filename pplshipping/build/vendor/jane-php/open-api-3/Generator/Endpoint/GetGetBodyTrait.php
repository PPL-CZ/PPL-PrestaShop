<?php

namespace PPLShipping\Jane\Component\OpenApi3\Generator\Endpoint;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\JsonSchemaRuntime\Reference;
use PPLShipping\Jane\Component\OpenApi3\Generator\RequestBodyGenerator;
use PPLShipping\Jane\Component\OpenApi3\Guesser\GuessClass;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\RequestBody;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Param;
use PPLShipping\PhpParser\Node\Stmt;
use PPLShipping\Symfony\Component\Serializer\SerializerInterface;
trait GetGetBodyTrait
{
    public function getGetBody(OperationGuess $operation, Context $context, GuessClass $guessClass, RequestBodyGenerator $requestBodyGenerator) : Stmt\ClassMethod
    {
        $opRef = $operation->getReference() . '/requestBody';
        $requestBody = $operation->getOperation()->getRequestBody();
        if ($requestBody instanceof Reference) {
            [$_, $requestBody] = $guessClass->resolve($requestBody, RequestBody::class);
        }
        return new Stmt\ClassMethod('getBody', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'params' => [new Param(new Expr\Variable('serializer'), null, new Name\FullyQualified(SerializerInterface::class)), new Param(new Expr\Variable('streamFactory'), new Expr\ConstFetch(new Name('null')))], 'returnType' => new Name('array'), 'stmts' => $requestBodyGenerator->getSerializeStatements($requestBody, $opRef, $context)]);
    }
}
