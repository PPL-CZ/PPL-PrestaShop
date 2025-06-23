<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser\JsonSchema;

use PPLShipping\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\ChainGuesserAwareTrait;
use PPLShipping\Jane\Component\JsonSchema\Guesser\ClassGuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\Guesser\GuesserInterface;
use PPLShipping\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLShipping\Jane\Component\JsonSchema\Registry\Registry;
class AdditionalItemsGuesser implements ChainGuesserAwareInterface, GuesserInterface, ClassGuesserInterface
{
    use ChainGuesserAwareTrait;
    /**
     * {@inheritdoc}
     */
    public function guessClass($object, string $name, string $reference, Registry $registry) : void
    {
        $this->chainGuesser->guessClass($object->getAdditionalItems(), $name . 'AdditionalItems', $reference . '/additionalItems', $registry);
    }
    /**
     * {@inheritdoc}
     */
    public function supportObject($object) : bool
    {
        return $object instanceof JsonSchema && $object->getAdditionalItems() instanceof JsonSchema;
    }
}
