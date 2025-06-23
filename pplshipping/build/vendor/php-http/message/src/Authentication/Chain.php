<?php

namespace PPLShipping\Http\Message\Authentication;

use PPLShipping\Http\Message\Authentication;
use PPLShipping\Psr\Http\Message\RequestInterface;
/**
 * Authenticate a PSR-7 Request with a multiple authentication methods.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class Chain implements Authentication
{
    /**
     * @var Authentication[]
     */
    private $authenticationChain = [];
    /**
     * @param Authentication[] $authenticationChain
     */
    public function __construct(array $authenticationChain = [])
    {
        foreach ($authenticationChain as $authentication) {
            if (!$authentication instanceof Authentication) {
                throw new \InvalidArgumentException('PPLShipping\\Members of the authentication chain must be of type Http\\Message\\Authentication');
            }
        }
        $this->authenticationChain = $authenticationChain;
    }
    public function authenticate(RequestInterface $request)
    {
        foreach ($this->authenticationChain as $authentication) {
            $request = $authentication->authenticate($request);
        }
        return $request;
    }
}
