<?php

namespace PPLShipping\Http\Message\MessageFactory;

use PPLShipping\Http\Message\MessageFactory;
use PPLShipping\Http\Message\StreamFactory\DiactorosStreamFactory;
use PPLShipping\Laminas\Diactoros\Request as LaminasRequest;
use PPLShipping\Laminas\Diactoros\Response as LaminasResponse;
use PPLShipping\Zend\Diactoros\Request as ZendRequest;
use PPLShipping\Zend\Diactoros\Response as ZendResponse;
if (!\interface_exists(MessageFactory::class)) {
    throw new \LogicException('You cannot use "Http\\Message\\MessageFactory\\DiactorosMessageFactory" as the "php-http/message-factory" package is not installed. Try running "composer require php-http/message-factory". Note that this package is deprecated, use "psr/http-factory" instead');
}
/**
 * Creates Diactoros messages.
 *
 * @author GeLo <geloen.eric@gmail.com>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Diactoros PSR-17 factory
 */
final class DiactorosMessageFactory implements MessageFactory
{
    /**
     * @var DiactorosStreamFactory
     */
    private $streamFactory;
    public function __construct()
    {
        $this->streamFactory = new DiactorosStreamFactory();
    }
    public function createRequest($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        if (\class_exists(LaminasRequest::class)) {
            return (new LaminasRequest($uri, $method, $this->streamFactory->createStream($body), $headers))->withProtocolVersion($protocolVersion);
        }
        return (new ZendRequest($uri, $method, $this->streamFactory->createStream($body), $headers))->withProtocolVersion($protocolVersion);
    }
    public function createResponse($statusCode = 200, $reasonPhrase = null, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        if (\class_exists(LaminasResponse::class)) {
            return (new LaminasResponse($this->streamFactory->createStream($body), $statusCode, $headers))->withProtocolVersion($protocolVersion);
        }
        return (new ZendResponse($this->streamFactory->createStream($body), $statusCode, $headers))->withProtocolVersion($protocolVersion);
    }
}
