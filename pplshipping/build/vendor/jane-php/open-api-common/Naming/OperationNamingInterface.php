<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Naming;

use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
interface OperationNamingInterface
{
    public function getFunctionName(OperationGuess $operation) : string;
    public function getEndpointName(OperationGuess $operation) : string;
}
