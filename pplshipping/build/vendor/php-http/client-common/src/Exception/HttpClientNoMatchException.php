<?php

declare (strict_types=1);
namespace PPLShipping\Http\Client\Common\Exception;

use PPLShipping\Http\Client\Exception\TransferException;
use PPLShipping\Psr\Http\Message\RequestInterface;
/**
 * Thrown when a http client match in the HTTPClientRouter.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class HttpClientNoMatchException extends TransferException
{
    /**
     * @var RequestInterface
     */
    private $request;
    public function __construct(string $message, RequestInterface $request, ?\Exception $previous = null)
    {
        $this->request = $request;
        parent::__construct($message, 0, $previous);
    }
    public function getRequest() : RequestInterface
    {
        return $this->request;
    }
}
