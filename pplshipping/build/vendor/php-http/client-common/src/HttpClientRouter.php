<?php

declare (strict_types=1);
namespace PPLShipping\Http\Client\Common;

use PPLShipping\Http\Client\Common\Exception\HttpClientNoMatchException;
use PPLShipping\Http\Client\HttpAsyncClient;
use PPLShipping\Http\Message\RequestMatcher;
use PPLShipping\Psr\Http\Client\ClientInterface;
use PPLShipping\Psr\Http\Message\RequestInterface;
use PPLShipping\Psr\Http\Message\ResponseInterface;
/**
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class HttpClientRouter implements HttpClientRouterInterface
{
    /**
     * @var (array{matcher: RequestMatcher, client: FlexibleHttpClient})[]
     */
    private $clients = [];
    public function sendRequest(RequestInterface $request) : ResponseInterface
    {
        return $this->chooseHttpClient($request)->sendRequest($request);
    }
    public function sendAsyncRequest(RequestInterface $request)
    {
        return $this->chooseHttpClient($request)->sendAsyncRequest($request);
    }
    /**
     * Add a client to the router.
     *
     * @param ClientInterface|HttpAsyncClient $client
     */
    public function addClient($client, RequestMatcher $requestMatcher) : void
    {
        if (!$client instanceof ClientInterface && !$client instanceof HttpAsyncClient) {
            throw new \TypeError(\sprintf('%s::addClient(): Argument #1 ($client) must be of type %s|%s, %s given', self::class, ClientInterface::class, HttpAsyncClient::class, \get_debug_type($client)));
        }
        $this->clients[] = ['matcher' => $requestMatcher, 'client' => new FlexibleHttpClient($client)];
    }
    /**
     * Choose an HTTP client given a specific request.
     */
    private function chooseHttpClient(RequestInterface $request) : FlexibleHttpClient
    {
        foreach ($this->clients as $client) {
            if ($client['matcher']->matches($request)) {
                return $client['client'];
            }
        }
        throw new HttpClientNoMatchException('No client found for the specified request', $request);
    }
}
