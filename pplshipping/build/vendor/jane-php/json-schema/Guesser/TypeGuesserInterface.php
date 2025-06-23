<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser;

use PPLShipping\Jane\Component\JsonSchema\Guesser\Guess\Type;
use PPLShipping\Jane\Component\JsonSchema\Registry\Registry;
interface TypeGuesserInterface
{
    /**
     * Return all types guessed.
     *
     * @internal
     */
    public function guessType($object, string $name, string $reference, Registry $registry) : Type;
}
