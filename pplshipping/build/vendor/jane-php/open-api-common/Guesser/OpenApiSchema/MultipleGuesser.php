<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Guesser\OpenApiSchema;

use PPLShipping\Jane\Component\JsonSchema\Guesser\JsonSchema\MultipleGuesser as BaseMultipleGuesser;
class MultipleGuesser extends BaseMultipleGuesser
{
    use SchemaClassTrait;
    public function __construct(string $schemaClass, array $bannedTypes = [])
    {
        $this->schemaClass = $schemaClass;
        $this->bannedTypes = $bannedTypes;
    }
}
