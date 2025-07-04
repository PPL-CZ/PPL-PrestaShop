<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Guesser\OpenApiSchema;

use PPLShipping\Jane\Component\JsonSchema\Guesser\JsonSchema\CustomStringFormatGuesser as BaseCustomStringFormatGuesser;
class CustomStringFormatGuesser extends BaseCustomStringFormatGuesser
{
    use SchemaClassTrait;
    public function __construct(string $schemaClass, array $mapping)
    {
        parent::__construct($mapping);
        $this->schemaClass = $schemaClass;
    }
}
