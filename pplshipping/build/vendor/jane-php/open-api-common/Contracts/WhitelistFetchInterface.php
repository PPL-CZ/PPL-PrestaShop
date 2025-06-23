<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Contracts;

use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLShipping\Jane\Component\OpenApiCommon\Registry\Registry;
interface WhitelistFetchInterface
{
    public function addOperationRelations(OperationGuess $operationGuess, Registry $registry);
}
