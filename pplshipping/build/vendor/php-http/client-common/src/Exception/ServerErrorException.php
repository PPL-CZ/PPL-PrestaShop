<?php

declare (strict_types=1);
namespace PPLShipping\Http\Client\Common\Exception;

use PPLShipping\Http\Client\Exception\HttpException;
/**
 * Thrown when there is a server error (5xx).
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class ServerErrorException extends HttpException
{
}
