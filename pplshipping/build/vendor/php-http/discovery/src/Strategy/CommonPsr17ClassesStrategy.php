<?php

namespace PPLShipping\Http\Discovery\Strategy;

use PPLShipping\Psr\Http\Message\RequestFactoryInterface;
use PPLShipping\Psr\Http\Message\ResponseFactoryInterface;
use PPLShipping\Psr\Http\Message\ServerRequestFactoryInterface;
use PPLShipping\Psr\Http\Message\StreamFactoryInterface;
use PPLShipping\Psr\Http\Message\UploadedFileFactoryInterface;
use PPLShipping\Psr\Http\Message\UriFactoryInterface;
/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * Don't miss updating src/Composer/Plugin.php when adding a new supported class.
 */
final class CommonPsr17ClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [RequestFactoryInterface::class => ['PPLShipping\\Phalcon\\Http\\Message\\RequestFactory', 'PPLShipping\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLShipping\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLShipping\\Http\\Factory\\Diactoros\\RequestFactory', 'PPLShipping\\Http\\Factory\\Guzzle\\RequestFactory', 'PPLShipping\\Http\\Factory\\Slim\\RequestFactory', 'PPLShipping\\Laminas\\Diactoros\\RequestFactory', 'PPLShipping\\Slim\\Psr7\\Factory\\RequestFactory', 'PPLShipping\\HttpSoft\\Message\\RequestFactory'], ResponseFactoryInterface::class => ['PPLShipping\\Phalcon\\Http\\Message\\ResponseFactory', 'PPLShipping\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLShipping\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLShipping\\Http\\Factory\\Diactoros\\ResponseFactory', 'PPLShipping\\Http\\Factory\\Guzzle\\ResponseFactory', 'PPLShipping\\Http\\Factory\\Slim\\ResponseFactory', 'PPLShipping\\Laminas\\Diactoros\\ResponseFactory', 'PPLShipping\\Slim\\Psr7\\Factory\\ResponseFactory', 'PPLShipping\\HttpSoft\\Message\\ResponseFactory'], ServerRequestFactoryInterface::class => ['PPLShipping\\Phalcon\\Http\\Message\\ServerRequestFactory', 'PPLShipping\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLShipping\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLShipping\\Http\\Factory\\Diactoros\\ServerRequestFactory', 'PPLShipping\\Http\\Factory\\Guzzle\\ServerRequestFactory', 'PPLShipping\\Http\\Factory\\Slim\\ServerRequestFactory', 'PPLShipping\\Laminas\\Diactoros\\ServerRequestFactory', 'PPLShipping\\Slim\\Psr7\\Factory\\ServerRequestFactory', 'PPLShipping\\HttpSoft\\Message\\ServerRequestFactory'], StreamFactoryInterface::class => ['PPLShipping\\Phalcon\\Http\\Message\\StreamFactory', 'PPLShipping\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLShipping\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLShipping\\Http\\Factory\\Diactoros\\StreamFactory', 'PPLShipping\\Http\\Factory\\Guzzle\\StreamFactory', 'PPLShipping\\Http\\Factory\\Slim\\StreamFactory', 'PPLShipping\\Laminas\\Diactoros\\StreamFactory', 'PPLShipping\\Slim\\Psr7\\Factory\\StreamFactory', 'PPLShipping\\HttpSoft\\Message\\StreamFactory'], UploadedFileFactoryInterface::class => ['PPLShipping\\Phalcon\\Http\\Message\\UploadedFileFactory', 'PPLShipping\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLShipping\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLShipping\\Http\\Factory\\Diactoros\\UploadedFileFactory', 'PPLShipping\\Http\\Factory\\Guzzle\\UploadedFileFactory', 'PPLShipping\\Http\\Factory\\Slim\\UploadedFileFactory', 'PPLShipping\\Laminas\\Diactoros\\UploadedFileFactory', 'PPLShipping\\Slim\\Psr7\\Factory\\UploadedFileFactory', 'PPLShipping\\HttpSoft\\Message\\UploadedFileFactory'], UriFactoryInterface::class => ['PPLShipping\\Phalcon\\Http\\Message\\UriFactory', 'PPLShipping\\Nyholm\\Psr7\\Factory\\Psr17Factory', 'PPLShipping\\GuzzleHttp\\Psr7\\HttpFactory', 'PPLShipping\\Http\\Factory\\Diactoros\\UriFactory', 'PPLShipping\\Http\\Factory\\Guzzle\\UriFactory', 'PPLShipping\\Http\\Factory\\Slim\\UriFactory', 'PPLShipping\\Laminas\\Diactoros\\UriFactory', 'PPLShipping\\Slim\\Psr7\\Factory\\UriFactory', 'PPLShipping\\HttpSoft\\Message\\UriFactory']];
    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }
        return $candidates;
    }
}
