<?php

namespace PPLShipping\Jane\Component\OpenApi3;

use PPLShipping\Jane\Component\JsonSchemaRuntime\Reference;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\MediaType;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\Parameter;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\RequestBody;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\Response;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\Schema as SchemaModel;
use PPLShipping\Jane\Component\OpenApiCommon\Contracts\WhitelistFetchInterface;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\GuessClass;
use PPLShipping\Jane\Component\OpenApiCommon\Naming\ChainOperationNaming;
use PPLShipping\Jane\Component\OpenApiCommon\Naming\OperationIdNaming;
use PPLShipping\Jane\Component\OpenApiCommon\Naming\OperationUrlNaming;
use PPLShipping\Jane\Component\OpenApiCommon\Registry\Registry;
use PPLShipping\Jane\Component\OpenApiCommon\Registry\Schema;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
class WhitelistedSchema implements WhitelistFetchInterface
{
    private $schema;
    private $naming;
    private $guessClass;
    public function __construct(Schema $schema, DenormalizerInterface $denormalizer)
    {
        $this->schema = $schema;
        $this->naming = new ChainOperationNaming([new OperationIdNaming(), new OperationUrlNaming()]);
        $this->guessClass = new GuessClass(SchemaModel::class, $denormalizer);
    }
    public function addOperationRelations(OperationGuess $operationGuess, Registry $registry) : void
    {
        $baseOperation = $this->naming->getEndpointName($operationGuess);
        if ($this->schema->relationExists($baseOperation)) {
            return;
        }
        /** @var RequestBody|null $requestBody */
        $requestBody = $operationGuess->getOperation()->getRequestBody();
        if (null !== $requestBody) {
            if (null !== $requestBody->getContent() && \is_iterable($requestBody->getContent())) {
                /** @var MediaType $content */
                foreach ($requestBody->getContent() as $contentType => $content) {
                    if (\in_array($contentType, ['application/json', 'application/x-www-form-urlencoded'], \true)) {
                        $contentReference = $operationGuess->getReference() . '/content/' . $contentType . '/schema';
                        $schema = $content->getSchema();
                        $classGuess = $this->guessClass->guessClass($schema, $contentReference, $registry);
                        if (null !== $classGuess) {
                            $this->schema->addOperationRelation($baseOperation, $classGuess->getName());
                        }
                    }
                }
            }
        }
        /** @var Response[]|null $responses */
        $responses = $operationGuess->getOperation()->getResponses();
        if (null !== $responses && \count($responses) > 0) {
            foreach ($responses as $response) {
                if ($response instanceof Reference) {
                    [$_, $response] = $this->guessClass->resolve($response, Response::class);
                }
                if (!$response instanceof Response) {
                    continue;
                }
                if (null === $response->getContent()) {
                    $schema = null;
                    $classGuess = $this->guessClass->guessClass($schema, $operationGuess->getReference(), $registry);
                    if (null !== $classGuess) {
                        $this->schema->addOperationRelation($baseOperation, $classGuess->getName());
                    }
                }
                if (null !== $response->getContent() && \is_iterable($response->getContent())) {
                    /** @var MediaType $content */
                    foreach ($response->getContent() as $contentType => $content) {
                        if ('application/json' === $contentType) {
                            $contentReference = $operationGuess->getReference() . '/content/' . $contentType . '/schema';
                            $schema = $content->getSchema();
                            $classGuess = $this->guessClass->guessClass($schema, $contentReference, $registry);
                            if (null !== $classGuess) {
                                $this->schema->addOperationRelation($baseOperation, $classGuess->getName());
                            }
                        }
                    }
                }
            }
        }
        /** @var Parameter[]|null $parameters */
        $parameters = $operationGuess->getOperation()->getParameters();
        if (null !== $parameters && \count($parameters) > 0) {
            foreach ($parameters as $key => $parameter) {
                if ($parameter instanceof Parameter && 'body' === $parameter->getIn()) {
                    $reference = $operationGuess->getReference() . '/parameters/' . $key;
                    $schema = $parameter->getSchema();
                    $classGuess = $this->guessClass->guessClass($schema, $reference, $registry);
                    if (null !== $classGuess) {
                        $this->schema->addOperationRelation($baseOperation, $classGuess->getName());
                    }
                }
            }
        }
    }
}
