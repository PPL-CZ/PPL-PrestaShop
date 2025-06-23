<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLShipping\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareTrait;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\MultipleType;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\TypeGuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLShipping\Jane\Component\JsonSchema\Registry\Registry;
class OneOfGuesser implements ChainGuesserAwareInterface, TypeGuesserInterface, GuesserInterface
{
    use ChainGuesserAwareTrait;
    /**
     * {@inheritdoc}
     */
    public function supportObject($object) : bool
    {
        return $object instanceof JsonSchema && 'object' !== $object->getType() && \is_array($object->getOneOf()) && \count($object->getOneOf()) > 0;
    }
    /**
     * {@inheritdoc}
     */
    public function guessType($object, string $name, string $reference, Registry $registry) : Type
    {
        $type = new MultipleType($object);
        foreach ($object->getOneOf() as $oneOfKey => $oneOf) {
            $type->addType($this->chainGuesser->guessType($oneOf, $name, $reference . '/oneOf/' . $oneOfKey, $registry));
        }
        return $type;
    }
}
