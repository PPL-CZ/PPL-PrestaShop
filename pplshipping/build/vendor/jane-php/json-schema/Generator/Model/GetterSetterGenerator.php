<?php

namespace PPLShipping\Jane\Component\JsonSchema\Generator\Model;

use PPLShipping\Jane\Component\JsonSchema\Generator\Naming;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\MultipleType;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\PhpParser\Comment\Doc;
use PPLShipping\PhpParser\Node\Expr;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Param;
use PPLShipping\PhpParser\Node\Scalar;
use PPLShipping\PhpParser\Node\Stmt;
trait GetterSetterGenerator
{
    /**
     * The naming service.
     */
    protected abstract function getNaming() : Naming;
    protected function createGetter(Property $property, string $namespace, bool $strict) : Stmt\ClassMethod
    {
        $returnType = $property->getType()->getTypeHint($namespace);
        if ($returnType && (!$strict || $property->isNullable())) {
            $returnType = '?' . $returnType;
        }
        return new Stmt\ClassMethod(
            // getProperty
            $this->getNaming()->getPrefixedMethodName('get', $property->getAccessorName()),
            [
                // public function
                'type' => Stmt\Class_::MODIFIER_PUBLIC,
                'stmts' => [
                    // return $this->property;
                    new Stmt\Return_(new Expr\PropertyFetch(new Expr\Variable('this'), $property->getPhpName())),
                ],
                'returnType' => $returnType,
            ],
            ['comments' => [$this->createGetterDoc($property, $namespace, $strict)]]
        );
    }
    protected function createSetter(Property $property, string $namespace, bool $strict, bool $fluent = \true) : Stmt\ClassMethod
    {
        $setType = $property->getType()->getTypeHint($namespace);
        if ($setType && (!$strict || $property->isNullable())) {
            $setType = '?' . $setType;
        }
        $stmts = [
            new Stmt\Expression(new Expr\Assign(new Expr\ArrayDimFetch(new Expr\PropertyFetch(new Expr\Variable('this'), 'initialized'), new Scalar\String_($property->getPhpName())), new Expr\ConstFetch(new Name('true')))),
            // $this->property = $property;
            new Stmt\Expression(new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('this'), $property->getPhpName()), new Expr\Variable($property->getPhpName()))),
        ];
        if ($fluent) {
            // return $this;
            $stmts[] = new Stmt\Return_(new Expr\Variable('this'));
        }
        return new Stmt\ClassMethod(
            // setProperty
            $this->getNaming()->getPrefixedMethodName('set', $property->getAccessorName()),
            [
                // public function
                'type' => Stmt\Class_::MODIFIER_PUBLIC,
                // ($property)
                'params' => [new Param(new Expr\Variable($property->getPhpName()), null, $setType)],
                'stmts' => $stmts,
                'returnType' => $fluent ? 'self' : null,
            ],
            ['comments' => [$this->createSetterDoc($property, $namespace, $strict, $fluent)]]
        );
    }
    protected function createGetterDoc(Property $property, string $namespace, bool $strict) : Doc
    {
        $description = \sprintf(<<<EOD
/**
 * %s
 *

EOD
, $property->getDescription());
        if ($property->isDeprecated()) {
            $description .= <<<EOD
 * @deprecated
 *

EOD;
        }
        $description .= \sprintf(<<<EOD
 * @return %s
 */
EOD
, $this->getDocType($property, $namespace, $strict));
        return new Doc($description);
    }
    protected function createSetterDoc(Property $property, string $namespace, bool $strict, bool $fluent) : Doc
    {
        $description = \sprintf(<<<EOD
/**
 * %s
 *
 * @param %s %s

EOD
, $property->getDescription(), $this->getDocType($property, $namespace, $strict), '$' . $property->getPhpName());
        if ($property->isDeprecated()) {
            $description .= <<<EOD
 *
 * @deprecated

EOD;
        }
        if ($fluent) {
            $description .= <<<EOD
 *
 * @return self

EOD;
        }
        $description .= <<<EOD
 */
EOD;
        return new Doc($description);
    }
    private function getDocType(Property $property, string $namespace, bool $strict) : string
    {
        $returnType = $property->getType();
        $returnTypeHint = $returnType->getDocTypeHint($namespace);
        if ($strict && !$property->isNullable()) {
            return $returnTypeHint;
        }
        $returnTypes = [$returnType];
        if ($returnType instanceof MultipleType) {
            $returnTypes = $returnType->getTypes();
        }
        if (!\count(\array_intersect([Type::TYPE_MIXED, Type::TYPE_NULL], $returnTypes))) {
            $returnTypeHint .= '|' . Type::TYPE_NULL;
        }
        return $returnTypeHint;
    }
}
