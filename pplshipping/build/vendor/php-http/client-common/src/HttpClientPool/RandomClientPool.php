<?php

declare (strict_types=1);
namespace PPLShipping\Http\Client\Common\HttpClientPool;

use PPLShipping\Http\Client\Common\Exception\HttpClientNotFoundException;
/**
 * RandomClientPool will choose a random enabled client in the pool.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class RandomClientPool extends HttpClientPool
{
    protected function chooseHttpClient() : HttpClientPoolItem
    {
        $clientPool = \array_filter($this->clientPool, function (HttpClientPoolItem $clientPoolItem) {
            return !$clientPoolItem->isDisabled();
        });
        if (0 === \count($clientPool)) {
            throw new HttpClientNotFoundException('Cannot choose a http client as there is no one present in the pool');
        }
        return $clientPool[\array_rand($clientPool)];
    }
}
