<?php

namespace PPLShipping;

use PPLShipping\Jane\Component\OpenApiRuntime\Client\Plugin\AuthenticationRegistry;
use PPLShipping\Psr\Http\Client\ClientInterface;
use PPLShipping\Psr\Http\Message\RequestFactoryInterface;
use PPLShipping\Psr\Http\Message\ResponseInterface;
use PPLShipping\Psr\Http\Message\StreamFactoryInterface;
use PPLShipping\Psr\Http\Message\StreamInterface;
use PPLShipping\Symfony\Component\Serializer\SerializerInterface;
abstract class Client
{
    public const FETCH_RESPONSE = 'response';
    public const FETCH_OBJECT = 'object';
    /**
     * @var ClientInterface
     */
    protected $httpClient;
    /**
     * @var RequestFactoryInterface
     */
    protected $requestFactory;
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var StreamFactoryInterface
     */
    protected $streamFactory;
    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory, SerializerInterface $serializer, StreamFactoryInterface $streamFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->serializer = $serializer;
        $this->streamFactory = $streamFactory;
    }
    public function executeEndpoint(Endpoint $endpoint, string $fetch = self::FETCH_OBJECT)
    {
        if (self::FETCH_RESPONSE === $fetch) {
            trigger_deprecation('jane-php/open-api-common', '7.3', 'Using %s::%s method with $fetch parameter equals to response is deprecated, use %s::%s instead.', __CLASS__, __METHOD__, __CLASS__, 'executeRawEndpoint');
            return $this->executeRawEndpoint($endpoint);
        }
        return $endpoint->parseResponse($this->processEndpoint($endpoint), $this->serializer, $fetch);
    }
    public function executeRawEndpoint(Endpoint $endpoint) : ResponseInterface
    {
        return $this->processEndpoint($endpoint);
    }
    private function processEndpoint(Endpoint $endpoint) : ResponseInterface
    {
        [$bodyHeaders, $body] = $endpoint->getBody($this->serializer, $this->streamFactory);
        $queryString = $endpoint->getQueryString();
        $uriGlue = \false === \strpos($endpoint->getUri(), '?') ? '?' : '&';
        $uri = $queryString !== '' ? $endpoint->getUri() . $uriGlue . $queryString : $endpoint->getUri();
        $request = $this->requestFactory->createRequest($endpoint->getMethod(), $uri);
        if ($body) {
            if ($body instanceof StreamInterface) {
                $request = $request->withBody($body);
            } elseif (\is_resource($body)) {
                $request = $request->withBody($this->streamFactory->createStreamFromResource($body));
            } elseif (\strlen($body) <= 4000 && @\file_exists($body)) {
                // more than 4096 chars will trigger an error
                $request = $request->withBody($this->streamFactory->createStreamFromFile($body));
            } else {
                $request = $request->withBody($this->streamFactory->createStream($body));
            }
        }
        foreach ($endpoint->getHeaders($bodyHeaders) as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        if (\count($endpoint->getAuthenticationScopes()) > 0) {
            $scopes = [];
            foreach ($endpoint->getAuthenticationScopes() as $scope) {
                $scopes[] = $scope;
            }
            $request = $request->withHeader(AuthenticationRegistry::SCOPES_HEADER, $scopes);
        }
        return $this->httpClient->sendRequest($request);
    }
}
\class_alias('PPLShipping\\Client', 'Client', \false);
