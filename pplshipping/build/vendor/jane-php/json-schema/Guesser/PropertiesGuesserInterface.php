<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser;

use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Property;
use PPLShipping\Jane\Component\JsonSchema\Registry\Registry;
interface PropertiesGuesserInterface
{
    /**
     * Return all properties guessed.
     *
     * @internal
     *
     * @return Property[]
     */
    public function guessProperties($object, string $name, string $reference, Registry $registry) : array;
}
