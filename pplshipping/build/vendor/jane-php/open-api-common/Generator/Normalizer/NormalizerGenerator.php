<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator\Normalizer;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\JsonSchema\Generator\Normalizer\NormalizerGenerator as JsonSchemaNormalizerGenerator;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\ParentClass;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Param;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
trait NormalizerGenerator
{
    use JsonSchemaNormalizerGenerator {
        normalizeMethodStatements as jsonSchemaNormalizeMethodStatements;
    }
    protected function normalizeMethodStatements(Expr\Variable $dataVariable, ClassGuess $classGuess, Context $context) : array
    {
        $statements = $this->jsonSchemaNormalizeMethodStatements($dataVariable, $classGuess, $context);
        if ($classGuess instanceof ParentClass) {
            foreach ($classGuess->getChildEntryKeys() as $discriminatorValue) {
                $objectVar = new Expr\Variable('object');
                $propertyVar = new Expr\MethodCall($objectVar, $this->getNaming()->getPrefixedMethodName('get', $classGuess->getDiscriminator()));
                $statements[] = new Stmt\If_(new Expr\BinaryOp\LogicalAnd(new Expr\BinaryOp\NotIdentical(new Expr\ConstFetch(new Name('null')), $propertyVar), new Expr\BinaryOp\Identical(new Scalar\String_($discriminatorValue), $propertyVar)), ['stmts' => [new Stmt\Return_(new Expr\MethodCall(new Expr\PropertyFetch(new Expr\Variable('this'), 'normalizer'), 'normalize', [$objectVar, new Expr\Variable('format'), new Expr\Variable('context')]))]]);
            }
        }
        return $statements;
    }
    /**
     * We want stricly same class for OpenApi Normalizers since we can have inheritance and this could avoid
     * normalization to use child classes. This is why we use `get_class` and not `instanceof`.
     */
    protected function createSupportsNormalizationMethod(string $modelFqdn) : Stmt\ClassMethod
    {
        $exprTestClassFunction = function ($class) {
            return new Expr\BinaryOp\Identical(new Expr\FuncCall(new Name('get_class'), [new Expr\Variable('data')]), new Scalar\String_($class));
        };
        return new Stmt\ClassMethod('supportsNormalization', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'returnType' => 'bool', 'params' => [new Param(new Expr\Variable('data')), new Param(new Expr\Variable('format'), new Expr\ConstFetch(new Name('null'))), new Param(new Expr\Variable('context'), new Expr\Array_(), 'array')], 'stmts' => [new Stmt\Return_(new Expr\BinaryOp\BooleanAnd(new Expr\FuncCall(new Name('is_object'), [new Expr\Variable('data')]), $exprTestClassFunction($modelFqdn)))]]);
    }
}
