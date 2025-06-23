<?php

namespace PPLShipping\Jane\Component\OpenApi3\Guesser;

use PPLShipping\Jane\Component\JsonSchemaRuntime\Reference;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\Parameter;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\GuessClass as BaseGuessClass;
class GuessClass extends BaseGuessClass
{
    public function resolveParameter(Reference $parameter)
    {
        $result = $parameter;
        return $parameter->resolve(function ($value) use($result) {
            return $this->denormalizer->denormalize($value, Parameter::class, 'json', ['document-origin' => (string) $result->getMergedUri()->withFragment('')]);
        });
    }
}
