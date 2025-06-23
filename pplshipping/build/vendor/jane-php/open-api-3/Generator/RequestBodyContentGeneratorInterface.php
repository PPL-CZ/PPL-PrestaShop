<?php

namespace PPLShipping\Jane\Component\OpenApi3\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\MediaType;
use PPLShipping\PhpParser\Node;
interface RequestBodyContentGeneratorInterface
{
    public function getTypes(MediaType $content, string $reference, Context $context) : array;
    public function getTypeCondition(MediaType $content, string $reference, Context $context) : Node;
    public function getSerializeStatements(MediaType $content, string $contentType, string $reference, Context $context) : array;
}
