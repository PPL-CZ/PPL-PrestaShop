<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\Numeric;

use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\ClassGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ObjectCheckTrait;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ValidatorGuess;
use PPLShipping\Jane\Component\JsonSchema\Guesser\Validator\ValidatorInterface;
use PPLShipping\Jane\Component\JsonSchema\JsonSchema\Model\JsonSchema;
use PPLShipping\Symfony\Component\Validator\Constraints\LessThanOrEqual;
class MaximumValidator implements ValidatorInterface
{
    use ObjectCheckTrait;
    public function supports($object) : bool
    {
        return $this->checkObject($object) && (\is_array($object->getType()) ? \in_array('integer', $object->getType()) || \in_array('number', $object->getType()) : 'integer' === $object->getType() || 'number' === $object->getType()) && \is_numeric($object->getMaximum());
    }
    /**
     * @param JsonSchema          $object
     * @param ClassGuess|Property $guess
     */
    public function guess($object, string $name, $guess) : void
    {
        $guess->addValidatorGuess(new ValidatorGuess(LessThanOrEqual::class, ['value' => $object->getMaximum()]));
    }
}
