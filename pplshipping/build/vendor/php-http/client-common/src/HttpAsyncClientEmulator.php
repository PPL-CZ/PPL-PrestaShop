<?php

declare (strict_types=1);
namespace PPLShipping\Http\Client\Common;

use PPLShipping\Http\Client\Exception;
use PPLShipping\Http\Client\Promise;
use PPLShipping\Psr\Http\Message\RequestInterface;
use PPLShipping\Psr\Http\Message\ResponseInterface;
/**
 * Emulates an HTTP Async Client in an HTTP Client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait HttpAsyncClientEmulator
{
    /**
     * @see HttpClient::sendRequest
     */
    public abstract function sendRequest(RequestInterface $request) : ResponseInterface;
    /**
     * @see HttpAsyncClient::sendAsyncRequest
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        try {
            return new Promise\HttpFulfilledPromise($this->sendRequest($request));
        } catch (Exception $e) {
            return new Promise\HttpRejectedPromise($e);
        }
    }
}
