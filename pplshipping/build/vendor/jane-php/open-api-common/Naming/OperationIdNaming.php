<?php

namespace PPLShipping\Jane\Component\OpenApiCommon\Naming;

use PPLShipping\Jane\Component\JsonSchema\Tools\InflectorTrait;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLShipping\Symfony\Component\String\Slugger\AsciiSlugger;
use PPLShipping\Symfony\Component\String\Slugger\SluggerInterface;
class OperationIdNaming implements OperationNamingInterface
{
    use InflectorTrait;
    /** @var SluggerInterface */
    private $slugger;
    public function __construct()
    {
        $this->slugger = new AsciiSlugger();
    }
    public function getFunctionName(OperationGuess $operation) : string
    {
        return $this->getInflector()->camelize($this->slugger->slug((string) $operation->getOperation()->getOperationId()));
    }
    public function getEndpointName(OperationGuess $operation) : string
    {
        $operationId = (string) $operation->getOperation()->getOperationId();
        $operationId = $this->slugger->slug($operationId, '-');
        return $this->getInflector()->classify($operationId);
    }
}
