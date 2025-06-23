<?php

namespace PPLShipping\Http\Message\UriFactory;

use PPLShipping\Http\Message\UriFactory;
use PPLShipping\Laminas\Diactoros\Uri as LaminasUri;
use PPLShipping\Psr\Http\Message\UriInterface;
use PPLShipping\Zend\Diactoros\Uri as ZendUri;
if (!\interface_exists(UriFactory::class)) {
    throw new \LogicException('You cannot use "Http\\Message\\MessageFactory\\DiactorosUriFactory" as the "php-http/message-factory" package is not installed. Try running "composer require php-http/message-factory". Note that this package is deprecated, use "psr/http-factory" instead');
}
/**
 * Creates Diactoros URI.
 *
 * @author David de Boer <david@ddeboer.nl>
 *
 * @deprecated This will be removed in php-http/message2.0. Consider using the official Diactoros PSR-17 factory
 */
final class DiactorosUriFactory implements UriFactory
{
    public function createUri($uri)
    {
        if ($uri instanceof UriInterface) {
            return $uri;
        } elseif (\is_string($uri)) {
            if (\class_exists(LaminasUri::class)) {
                return new LaminasUri($uri);
            }
            return new ZendUri($uri);
        }
        throw new \InvalidArgumentException('URI must be a string or UriInterface');
    }
}
