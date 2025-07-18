<?php

namespace PPLShipping\Http\Message\RequestMatcher;

use PPLShipping\Http\Message\RequestMatcher;
use PPLShipping\Psr\Http\Message\RequestInterface;
/**
 * Match a request with a callback.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class CallbackRequestMatcher implements RequestMatcher
{
    /**
     * @var callable
     */
    private $callback;
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
    public function matches(RequestInterface $request)
    {
        return (bool) \call_user_func($this->callback, $request);
    }
}
