<?php

declare (strict_types=1);
namespace PPLShipping\Jane\Component\OpenApiRuntime\Client;

use PPLShipping\Psr\Http\Message\RequestInterface;
interface AuthenticationPlugin
{
    public function authentication(RequestInterface $request) : RequestInterface;
    public function getScope() : string;
}
