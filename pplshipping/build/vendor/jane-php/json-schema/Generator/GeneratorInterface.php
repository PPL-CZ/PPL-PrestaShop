<?php

namespace PPLShipping\Jane\Component\JsonSchema\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\JsonSchema\Registry\Schema;
interface GeneratorInterface
{
    /**
     * Generate a set of files given an object and a context.
     */
    public function generate(Schema $object, string $className, Context $context) : void;
}
