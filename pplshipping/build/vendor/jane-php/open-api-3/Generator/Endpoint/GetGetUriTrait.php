<?php

namespace PPLShipping\Jane\Component\OpenApi3\Generator\Endpoint;

use PPLShipping\Jane\Component\JsonSchemaRuntime\Reference;
use PPLShipping\Jane\Component\OpenApi3\Generator\EndpointGenerator;
use PPLShipping\Jane\Component\OpenApi3\Guesser\GuessClass;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\Parameter;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLShipping\PhpParser\Node\Arg;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
trait GetGetUriTrait
{
    public function getGetUri(OperationGuess $operation, GuessClass $guessClass) : Stmt\ClassMethod
    {
        $names = [];
        foreach ($operation->getParameters() as $parameter) {
            if ($parameter instanceof Reference) {
                $parameter = $guessClass->resolveParameter($parameter);
            }
            if ($parameter instanceof Parameter && EndpointGenerator::IN_PATH === $parameter->getIn()) {
                // $url = str_replace('{param}', $param, $url)
                $names[] = $parameter->getName();
            }
        }
        if (\count($names) === 0) {
            return new Stmt\ClassMethod('getUri', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'stmts' => [new Stmt\Return_(new Scalar\String_($operation->getPath()))], 'returnType' => new Name('string')]);
        }
        return new Stmt\ClassMethod('getUri', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'stmts' => [new Stmt\Return_(new Expr\FuncCall(new Name('str_replace'), [new Arg(new Expr\Array_(\array_map(function ($name) {
            return new Scalar\String_('{' . $name . '}');
        }, $names))), new Arg(new Expr\Array_(\array_map(function ($name) {
            return new Expr\PropertyFetch(new Expr\Variable('this'), $name);
        }, $names))), new Arg(new Scalar\String_($operation->getPath()))]))], 'returnType' => new Name('string')]);
    }
}
