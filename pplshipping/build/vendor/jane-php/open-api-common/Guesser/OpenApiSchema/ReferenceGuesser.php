<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Guesser\OpenApiSchema;

use PPLShipping\Jane\Component\JsonSchema\Guesser\ReferenceGuesser as BaseReferenceGuesser;
use PPLShipping\Symfony\Component\Serializer\SerializerInterface;
class ReferenceGuesser extends BaseReferenceGuesser
{
    use SchemaClassTrait;
    public function __construct(SerializerInterface $serializer, string $schemaClass)
    {
        parent::__construct($serializer);
        $this->schemaClass = $schemaClass;
    }
}
