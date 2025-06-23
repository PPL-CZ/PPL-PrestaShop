<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\CustomObjectType;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLShipping\Jane\Component\JsonSchema\Registry\Registry;
class CustomStringFormatGuesser implements GuesserInterface, TypeGuesserInterface
{
    /**
     * @var array<string, string> key: format, value: classname of the normalizer
     */
    protected $mapping;
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }
    public function supportObject($object) : bool
    {
        $class = $this->getSchemaClass();
        return $object instanceof $class && 'string' === $object->getType() && \array_key_exists($object->getFormat(), $this->mapping);
    }
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        return new CustomObjectType($object, $this->mapping[$object->getFormat()], []);
    }
    protected function getSchemaClass() : string
    {
        return JsonSchema::class;
    }
}
