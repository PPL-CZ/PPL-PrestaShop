<?php

namespace PPLShipping\Jane\Component\JsonSchema\Guesser;

interface GuesserInterface
{
    /**
     * Is this object supported for the guesser.
     *
     * @internal
     */
    public function supportObject($object) : bool;
}
