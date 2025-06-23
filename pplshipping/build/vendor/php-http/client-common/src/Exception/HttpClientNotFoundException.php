<?php

declare (strict_types=1);
namespace PPLShipping\Http\Client\Common\Exception;

use PPLShipping\Http\Client\Exception\TransferException;
/**
 * Thrown when a http client cannot be chosen in a pool.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class HttpClientNotFoundException extends TransferException
{
}
