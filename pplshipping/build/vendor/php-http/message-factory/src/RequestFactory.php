<?php

namespace PPLShipping\Http\Message;

use PPLShipping\Psr\Http\Message\UriInterface;
use PPLShipping\Psr\Http\Message\RequestInterface;
use PPLShipping\Psr\Http\Message\StreamInterface;
/**
 * Factory for PSR-7 Request.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @deprecated since version 1.1, use Psr\Http\Message\RequestFactoryInterface instead.
 */
interface RequestFactory
{
    /**
     * Creates a new PSR-7 request.
     *
     * @param string                               $method
     * @param string|UriInterface                  $uri
     * @param array                                $headers
     * @param resource|string|StreamInterface|null $body
     * @param string                               $protocolVersion
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1');
}
