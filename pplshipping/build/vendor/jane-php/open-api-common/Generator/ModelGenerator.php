<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\ModelGenerator as BaseModelGenerator;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess as BaseClassGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Model\ClassGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\ClassGuess;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\ParentClass;
use PPLShipping\PhpParser\Node\Stmt;
class ModelGenerator extends BaseModelGenerator
{
    use ClassGenerator;
    protected function doCreateClassMethods(BaseClassGuess $classGuess, Property $property, string $namespace, bool $required) : array
    {
        $methods = [];
        $methods[] = $this->createGetter($property, $namespace, $required);
        $methods[] = $this->createSetter($property, $namespace, $required, $classGuess instanceof ParentClass ? \false : \true);
        return $methods;
    }
    protected function doCreateModel(BaseClassGuess $class, array $properties, array $methods) : Stmt\Class_
    {
        $extends = null;
        if ($class instanceof ClassGuess && $class->getParentClass() instanceof ParentClass) {
            $extends = $this->getNaming()->getClassName($class->getParentClass()->getName());
        }
        $classModel = $this->createModel($class->getName(), $properties, $methods, \count($class->getExtensionsType()) > 0, $class->isDeprecated(), $extends);
        return $classModel;
    }
}
