<?php

namespace PPLShipping\Jane\Component\JsonSchema\Generator\Normalizer;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\JsonSchema\Generator\Naming;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\MultipleType;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\PhpParser\Comment\Doc;
use PPLShipping\PhpParser\Node\Arg;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Param;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
trait NormalizerGenerator
{
    /**
     * The naming service.
     *
     * @return Naming
     */
    protected abstract function getNaming();
    protected function createNormalizerClass($name, $methods, $useCacheableSupportsMethod = \false)
    {
        $traits = [new Stmt\TraitUse([new Name('DenormalizerAwareTrait')]), new Stmt\TraitUse([new Name('NormalizerAwareTrait')]), new Stmt\TraitUse([new Name('CheckArray')]), new Stmt\TraitUse([new Name('ValidatorTrait')])];
        $implements = [new Name('DenormalizerInterface'), new Name('NormalizerInterface'), new Name('DenormalizerAwareInterface'), new Name('NormalizerAwareInterface')];
        if ($useCacheableSupportsMethod) {
            $implements[] = new Name('CacheableSupportsMethodInterface');
        }
        return new Stmt\Class_($this->getNaming()->getClassName($name), ['stmts' => \array_merge($traits, $methods), 'implements' => $implements]);
    }
    /**
     * Create method to check if denormalization is supported.
     *
     * @param string $modelFqdn Fully Qualified name of the model class denormalized
     *
     * @return Stmt\ClassMethod
     */
    protected function createSupportsNormalizationMethod(string $modelFqdn)
    {
        return new Stmt\ClassMethod('supportsNormalization', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'returnType' => 'bool', 'params' => [new Param(new Expr\Variable('data')), new Param(new Expr\Variable('format'), new Expr\ConstFetch(new Name('null'))), new Param(new Expr\Variable('context'), new Expr\Array_(), 'array')], 'stmts' => [new Stmt\Return_(new Expr\Instanceof_(new Expr\Variable('data'), new Name('\\' . $modelFqdn)))]]);
    }
    /**
     * Create the normalization method.
     *
     * @return Stmt\ClassMethod
     */
    protected function createNormalizeMethod(string $modelFqdn, Context $context, ClassGuess $classGuess, bool $skipNullValues = \true, bool $skipRequiredFields = \false)
    {
        $context->refreshScope();
        $dataVariable = new Expr\Variable('data');
        $objectVariable = new Expr\Variable('object');
        $statements = $this->normalizeMethodStatements($dataVariable, $classGuess, $context);
        /** @var Property $property */
        foreach ($classGuess->getProperties() as $property) {
            if (!$property->isReadOnly()) {
                $propertyVar = new Expr\MethodCall($objectVariable, $this->getNaming()->getPrefixedMethodName('get', $property->getAccessorName()));
                list($normalizationStatements, $outputVar) = $property->getType()->createNormalizationStatement($context, $propertyVar);
                $normalizationStatements[] = new Stmt\Expression(new Expr\Assign(new Expr\ArrayDimFetch($dataVariable, new Scalar\String_($property->getName())), $outputVar));
                if (!$skipRequiredFields && $property->isRequired()) {
                    $statements = \array_merge($statements, $normalizationStatements);
                    continue;
                }
                if (!$property->isRequired()) {
                    $statements[] = new Stmt\If_(new Expr\BinaryOp\BooleanAnd(new Expr\MethodCall($objectVariable, 'isInitialized', [new Arg(new Scalar\String_($property->getPhpName()))]), new Expr\BinaryOp\NotIdentical(new Expr\ConstFetch(new Name('null')), $propertyVar)), ['stmts' => $normalizationStatements]);
                } else {
                    $statements[] = new Stmt\If_(new Expr\BinaryOp\NotIdentical(new Expr\ConstFetch(new Name('null')), $propertyVar), ['stmts' => $normalizationStatements]);
                }
                if ((!$context->isStrict() || $property->isNullable() || $property->getType() instanceof MultipleType && \count(\array_intersect([Type::TYPE_NULL], $property->getType()->getTypes())) === 1 || $property->getType()->getName() === Type::TYPE_NULL) && !$skipNullValues) {
                    $statements[] = new Stmt\Else_([new Stmt\Expression(new Expr\Assign(new Expr\ArrayDimFetch($dataVariable, new Scalar\String_($property->getName())), new Expr\ConstFetch(new Name('null'))))]);
                }
            }
        }
        $patternCondition = [];
        $loopKeyVar = new Expr\Variable($context->getUniqueVariableName('key'));
        $loopValueVar = new Expr\Variable($context->getUniqueVariableName('value'));
        foreach ($classGuess->getExtensionsType() as $pattern => $type) {
            list($denormalizationStatements, $outputVar) = $type->createNormalizationStatement($context, $loopValueVar);
            $patternCondition[] = new Stmt\If_(new Expr\FuncCall(new Name('preg_match'), [new Arg(new Expr\ConstFetch(new Name("'/" . \str_replace('/', '\\/', $pattern) . "/'"))), new Arg(new Expr\Cast\String_($loopKeyVar))]), ['stmts' => \array_merge($denormalizationStatements, [new Stmt\Expression(new Expr\Assign(new Expr\ArrayDimFetch($dataVariable, $loopKeyVar), $outputVar))])]);
        }
        if (\count($patternCondition) > 0) {
            $statements[] = new Stmt\Foreach_($objectVariable, $loopValueVar, ['keyVar' => $loopKeyVar, 'stmts' => $patternCondition]);
        }
        if ($this->validation) {
            $schema = $context->getCurrentSchema();
            $contextVariable = new Expr\Variable('context');
            $constraintFqdn = $schema->getNamespace() . '\\Validator\\' . $this->naming->getConstraintName($classGuess->getName());
            $statements[] = new Stmt\If_(new Expr\BooleanNot(new Expr\BinaryOp\Coalesce(new Expr\ArrayDimFetch($contextVariable, new Scalar\String_('skip_validation')), new Expr\ConstFetch(new Name('false')))), ['stmts' => [new Stmt\Expression(new Expr\MethodCall(new Expr\Variable('this'), 'validate', [new Arg($dataVariable), new Arg(new Expr\New_(new Name('\\' . $constraintFqdn)))]))]]);
        }
        $statements[] = new Stmt\Return_($dataVariable);
        return new Stmt\ClassMethod('normalize', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'params' => [new Param($objectVariable), new Param(new Expr\Variable('format'), new Expr\ConstFetch(new Name('null'))), new Param(new Expr\Variable('context'), new Expr\Array_(), 'array')], 'stmts' => $statements], ['comments' => [new Doc(<<<EOD
/**
 * @return array|string|int|float|bool|\\ArrayObject|null
 */
EOD
)]]);
    }
    /**
     * Create method to say that hasCacheableSupportsMethod is supported.
     *
     * @return Stmt\ClassMethod
     */
    protected function createHasCacheableSupportsMethod()
    {
        return new Stmt\ClassMethod('hasCacheableSupportsMethod', ['type' => Stmt\Class_::MODIFIER_PUBLIC, 'returnType' => 'bool', 'stmts' => [new Stmt\Return_(new Expr\ConstFetch(new Name('true')))]]);
    }
    protected function normalizeMethodStatements(Expr\Variable $dataVariable, ClassGuess $classGuess, Context $context) : array
    {
        return [new Stmt\Expression(new Expr\Assign($dataVariable, new Expr\Array_()))];
    }
}
