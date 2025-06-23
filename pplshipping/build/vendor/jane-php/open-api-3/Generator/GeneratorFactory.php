<?php

namespace PPLShipping\Jane\Component\OpenApi3\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\GeneratorInterface;
use PPLShipping\Jane\Component\OpenApi3\Generator\Parameter\NonBodyParameterGenerator;
use PPLShipping\Jane\Component\OpenApi3\Generator\RequestBodyContent\DefaultBodyContentGenerator;
use PPLShipping\Jane\Component\OpenApi3\Generator\RequestBodyContent\FormBodyContentGenerator;
use PPLShipping\Jane\Component\OpenApi3\Generator\RequestBodyContent\JsonBodyContentGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\ExceptionGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\OperationGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Naming\ChainOperationNaming;
use PPLShipping\Jane\Component\OpenApiCommon\Naming\OperationIdNaming;
use PPLShipping\Jane\Component\OpenApiCommon\Naming\OperationUrlNaming;
use PPLShipping\PhpParser\ParserFactory;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
class GeneratorFactory
{
    public static function build(DenormalizerInterface $serializer, string $endpointGeneratorClass) : GeneratorInterface
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        $nonBodyParameter = new NonBodyParameterGenerator($serializer, $parser);
        $exceptionGenerator = new ExceptionGenerator();
        $operationNaming = new ChainOperationNaming([new OperationIdNaming(), new OperationUrlNaming()]);
        $defaultContentGenerator = new DefaultBodyContentGenerator($serializer);
        $requestBodyGenerator = new RequestBodyGenerator($defaultContentGenerator);
        $requestBodyGenerator->addRequestBodyGenerator(JsonBodyContentGenerator::JSON_TYPES, new JsonBodyContentGenerator($serializer));
        $requestBodyGenerator->addRequestBodyGenerator(['application/x-www-form-urlencoded', 'multipart/form-data'], new FormBodyContentGenerator($serializer));
        if (!\class_exists($endpointGeneratorClass)) {
            throw new \InvalidArgumentException(\sprintf('Unknown generator class %s', $endpointGeneratorClass));
        }
        $endpointGenerator = new $endpointGeneratorClass($operationNaming, $nonBodyParameter, $serializer, $exceptionGenerator, $requestBodyGenerator);
        $operationGenerator = new OperationGenerator($endpointGenerator);
        return new ClientGenerator($operationGenerator, $operationNaming);
    }
}
