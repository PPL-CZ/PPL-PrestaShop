<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Guesser\OpenApiSchema;

use PPLShipping\Jane\Component\JsonSchema\Generator\Naming;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess as BaseClassGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\JsonSchema\AllOfGuesser as BaseAllOfGuesser;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\ClassGuess;
use PPLShipping\Symfony\Component\Serializer\SerializerInterface;
class AllOfGuesser extends BaseAllOfGuesser
{
    /** @var string */
    private $schemaClass;
    public function __construct(SerializerInterface $serializer, Naming $naming, string $schemaClass)
    {
        parent::__construct($serializer, $naming);
        $this->schemaClass = $schemaClass;
    }
    protected function createClassGuess($object, $reference, $name, $extensions) : BaseClassGuess
    {
        return new ClassGuess($object, $reference, $this->naming->getClassName($name), $extensions);
    }
    protected function getSchemaClass() : string
    {
        return $this->schemaClass;
    }
}
