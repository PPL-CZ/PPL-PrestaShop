<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\Array_;

use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ObjectCheckTrait;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ValidatorGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ValidatorInterface;
use PPLShipping\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLShipping\Symfony\Component\Validator\Constraints\Count;
class MinItemsValidator implements ValidatorInterface
{
    use ObjectCheckTrait;
    public function supports($object) : bool
    {
        return $this->checkObject($object) && (\is_array($object->getType()) ? \in_array('array', $object->getType()) : 'array' === $object->getType()) && null !== $object->getMinItems();
    }
    /**
     * @param JsonSchema          $object
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void
    {
        $guess->addValidatorGuess(new ValidatorGuess(Count::class, ['min' => $object->getMinItems(), 'minMessage' => 'This array has not enough values. It should have {{ limit }} values or more.']));
    }
}
