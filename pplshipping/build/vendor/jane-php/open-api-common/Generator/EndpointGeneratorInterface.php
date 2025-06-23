<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
interface EndpointGeneratorInterface
{
    public function createEndpointClass(OperationGuess $operation, Context $context) : array;
}
