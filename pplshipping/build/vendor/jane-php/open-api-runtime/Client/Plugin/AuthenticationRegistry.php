<?php

namespace PPLShipping\Jane\Component\OpenApiRuntime\Client\Plugin;

use PPLShipping\Http\Client\Common\Plugin;
use PPLShipping\Http\Promise\Promise;
use PPLShipping\Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;
use PPLShipping\Psr\Http\Message\RequestInterface;
class AuthenticationRegistry implements Plugin
{
    public const SCOPES_HEADER = 'X-Jane-Authentication';
    /** @var AuthenticationPlugin[] */
    private $authenticationPlugins;
    public function __construct(array $authenticationPlugins)
    {
        $this->authenticationPlugins = $authenticationPlugins;
    }
    public function handleRequest(RequestInterface $request, callable $next, callable $first) : Promise
    {
        $scopes = $request->getHeader(self::SCOPES_HEADER);
        foreach ($this->authenticationPlugins as $authenticationPlugin) {
            if (\in_array($authenticationPlugin->getScope(), $scopes)) {
                $request = $authenticationPlugin->authentication($request);
            }
        }
        // clean headers
        $request = $request->withoutHeader(self::SCOPES_HEADER);
        return $next($request);
    }
}
