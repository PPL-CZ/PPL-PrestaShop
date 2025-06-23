<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\Array_;

use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ObjectCheckTrait;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ValidatorGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ValidatorInterface;
use PPLShipping\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLShipping\Symfony\Component\Validator\Constraints\Unique;
class UniqueItemsValidator implements ValidatorInterface
{
    use ObjectCheckTrait;
    public function supports($object) : bool
    {
        return $this->checkObject($object) && (\is_array($object->getType()) ? \in_array('array', $object->getType()) : 'array' === $object->getType()) && null !== $object->getUniqueItems();
    }
    /**
     * @param JsonSchema          $object
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void
    {
        if (!$object->getUniqueItems()) {
            return;
        }
        $guess->addValidatorGuess(new ValidatorGuess(Unique::class));
    }
}
