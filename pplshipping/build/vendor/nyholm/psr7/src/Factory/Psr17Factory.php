<?php

declare (strict_types=1);
namespace PPLShipping\Nyholm\Psr7\Factory;

use PPLShipping\Nyholm\Psr7\Request;
use PPLShipping\Nyholm\Psr7\Response;
use PPLShipping\Nyholm\Psr7\ServerRequest;
use PPLShipping\Nyholm\Psr7\Stream;
use PPLShipping\Nyholm\Psr7\UploadedFile;
use PPLShipping\Nyholm\Psr7\Uri;
use PPLShipping\Psr\Http\Message\RequestFactoryInterface;
use PPLShipping\Psr\Http\Message\RequestInterface;
use PPLShipping\Psr\Http\Message\ResponseFactoryInterface;
use PPLShipping\Psr\Http\Message\ResponseInterface;
use PPLShipping\Psr\Http\Message\ServerRequestFactoryInterface;
use PPLShipping\Psr\Http\Message\ServerRequestInterface;
use PPLShipping\Psr\Http\Message\StreamFactoryInterface;
use PPLShipping\Psr\Http\Message\StreamInterface;
use PPLShipping\Psr\Http\Message\UploadedFileFactoryInterface;
use PPLShipping\Psr\Http\Message\UploadedFileInterface;
use PPLShipping\Psr\Http\Message\UriFactoryInterface;
use PPLShipping\Psr\Http\Message\UriInterface;
/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 *
 * @final This class should never be extended. See https://github.com/Nyholm/psr7/blob/master/doc/final.md
 */
class Psr17Factory implements RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface, StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface
{
    public function createRequest(string $method, $uri) : RequestInterface
    {
        return new Request($method, $uri);
    }
    public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface
    {
        if (2 > \func_num_args()) {
            // This will make the Response class to use a custom reasonPhrase
            $reasonPhrase = null;
        }
        return new Response($code, [], null, '1.1', $reasonPhrase);
    }
    public function createStream(string $content = '') : StreamInterface
    {
        return Stream::create($content);
    }
    public function createStreamFromFile(string $filename, string $mode = 'r') : StreamInterface
    {
        if ('' === $filename) {
            throw new \RuntimeException('Path cannot be empty');
        }
        if (\false === ($resource = @\fopen($filename, $mode))) {
            if ('' === $mode || \false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], \true)) {
                throw new \InvalidArgumentException(\sprintf('The mode "%s" is invalid.', $mode));
            }
            throw new \RuntimeException(\sprintf('The file "%s" cannot be opened: %s', $filename, \error_get_last()['message'] ?? ''));
        }
        return Stream::create($resource);
    }
    public function createStreamFromResource($resource) : StreamInterface
    {
        return Stream::create($resource);
    }
    public function createUploadedFile(StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null) : UploadedFileInterface
    {
        if (null === $size) {
            $size = $stream->getSize();
        }
        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
    public function createUri(string $uri = '') : UriInterface
    {
        return new Uri($uri);
    }
    public function createServerRequest(string $method, $uri, array $serverParams = []) : ServerRequestInterface
    {
        return new ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
}
