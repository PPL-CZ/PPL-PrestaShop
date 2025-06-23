<?php

namespace PPLShipping\Jane\Component\OpenApi3\Generator;

use PPLShipping\Jane\Component\JsonSchema\Generator\Context\Context;
use PPLShipping\Jane\Component\JsonSchema\Generator\File;
use PPLShipping\Jane\Component\JsonSchema\Generator\Naming;
use PPLShipping\Jane\Component\OpenApi3\Generator\Endpoint\GetConstructorTrait;
use PPLShipping\Jane\Component\OpenApi3\Generator\Endpoint\GetGetBodyTrait;
use PPLShipping\Jane\Component\OpenApi3\Generator\Endpoint\GetGetExtraHeadersTrait;
use PPLShipping\Jane\Component\OpenApi3\Generator\Endpoint\GetGetOptionsResolverTrait;
use PPLShipping\Jane\Component\OpenApi3\Generator\Endpoint\GetGetUriTrait;
use PPLShipping\Jane\Component\OpenApi3\Generator\Endpoint\GetTransformResponseBodyTrait;
use PPLShipping\Jane\Component\OpenApi3\Generator\Parameter\NonBodyParameterGenerator;
use PPLShipping\Jane\Component\OpenApi3\Guesser\GuessClass;
use PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model\Schema;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Endpoint\GetAuthenticationScopesTrait;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Endpoint\GetGetMethodTrait;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\EndpointGeneratorInterface;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\ExceptionGenerator;
use PPLShipping\Jane\Component\OpenApiCommon\Generator\Traits\OptionResolverNormalizationTrait;
use PPLShipping\Jane\Component\OpenApiCommon\Guesser\Guess\OperationGuess;
use PPLShipping\Jane\Component\OpenApiCommon\Naming\OperationNamingInterface;
use PPLShipping\PhpParser\Node\Name;
use PPLShipping\PhpParser\Node\Stmt;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
class EndpointGenerator implements EndpointGeneratorInterface
{
    use GetAuthenticationScopesTrait;
    use GetConstructorTrait;
    use GetGetBodyTrait;
    use GetGetExtraHeadersTrait;
    use GetGetMethodTrait;
    use GetGetOptionsResolverTrait;
    use GetGetUriTrait;
    use GetTransformResponseBodyTrait;
    use OptionResolverNormalizationTrait;
    public const IN_PATH = 'path';
    public const IN_QUERY = 'query';
    public const IN_HEADER = 'header';
    /** @var OperationNamingInterface */
    private $operationNaming;
    /** @var NonBodyParameterGenerator */
    private $nonBodyParameterGenerator;
    /** @var ExceptionGenerator */
    private $exceptionGenerator;
    /** @var RequestBodyGenerator */
    private $requestBodyGenerator;
    /** @var DenormalizerInterface */
    private $denormalizer;
    /** @var GuessClass */
    private $guessClass;
    public function __construct(OperationNamingInterface $operationNaming, NonBodyParameterGenerator $nonBodyParameterGenerator, DenormalizerInterface $denormalizer, ExceptionGenerator $exceptionGenerator, RequestBodyGenerator $requestBodyGenerator)
    {
        $this->operationNaming = $operationNaming;
        $this->nonBodyParameterGenerator = $nonBodyParameterGenerator;
        $this->exceptionGenerator = $exceptionGenerator;
        $this->requestBodyGenerator = $requestBodyGenerator;
        $this->denormalizer = $denormalizer;
        $this->guessClass = new GuessClass(Schema::class, $denormalizer);
    }
    public function createEndpointClass(OperationGuess $operation, Context $context) : array
    {
        $naming = new Naming();
        $endpointName = $this->operationNaming->getEndpointName($operation);
        [$constructorMethod, $methodParams, $methodParamsDoc, $pathProperties] = $this->getConstructor($operation, $context, $this->guessClass, $this->nonBodyParameterGenerator, $this->requestBodyGenerator);
        [$transformBodyMethod, $outputTypes, $throwTypes] = $this->getTransformResponseBody($operation, $endpointName, $this->guessClass, $this->exceptionGenerator, $context);
        $class = new Stmt\Class_($endpointName, ['extends' => new Name\FullyQualified($naming->getRuntimeClassFQCN($context->getCurrentSchema()->getNamespace(), ['Client'], 'BaseEndpoint')), 'implements' => [new Name\FullyQualified($naming->getRuntimeClassFQCN($context->getCurrentSchema()->getNamespace(), ['Client'], 'Endpoint'))], 'stmts' => \array_merge($pathProperties, $constructorMethod === null ? [] : [$constructorMethod], [new Stmt\Use_([new Stmt\UseUse(new Name\FullyQualified($naming->getRuntimeClassFQCN($context->getCurrentSchema()->getNamespace(), ['Client'], 'EndpointTrait')))]), $this->getGetMethod($operation), $this->getGetUri($operation, $this->guessClass), $this->getGetBody($operation, $context, $this->guessClass, $this->requestBodyGenerator)])]);
        [$genericCustomQueryResolver, $operationCustomQueryResolver] = $this->customOptionResolvers($operation, $context);
        $extraHeadersMethod = $this->getExtraHeadersMethod($operation, $this->guessClass);
        $queryResolverMethod = $this->getOptionsResolverMethod($operation, self::IN_QUERY, 'getQueryOptionsResolver', $this->guessClass, $this->nonBodyParameterGenerator, $operationCustomQueryResolver, $genericCustomQueryResolver);
        $headerResolverMethod = $this->getOptionsResolverMethod($operation, self::IN_HEADER, 'getHeadersOptionsResolver', $this->guessClass, $this->nonBodyParameterGenerator);
        if ($extraHeadersMethod) {
            $class->stmts[] = $extraHeadersMethod;
        }
        if ($queryResolverMethod) {
            $class->stmts[] = $queryResolverMethod;
        }
        if ($headerResolverMethod) {
            $class->stmts[] = $headerResolverMethod;
        }
        $class->stmts[] = $transformBodyMethod;
        $class->stmts[] = $this->getAuthenticationScopesMethod($operation);
        $file = new File($context->getCurrentSchema()->getDirectory() . \DIRECTORY_SEPARATOR . 'Endpoint' . \DIRECTORY_SEPARATOR . $endpointName . '.php', new Stmt\Namespace_(new Name($context->getCurrentSchema()->getNamespace() . '\\Endpoint'), [$class]), 'Endpoint');
        $context->getCurrentSchema()->addFile($file);
        return [$context->getCurrentSchema()->getNamespace() . '\\Endpoint\\' . $endpointName, $methodParams, $methodParamsDoc, $outputTypes, $throwTypes];
    }
}
