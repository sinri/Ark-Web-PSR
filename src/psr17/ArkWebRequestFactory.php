<?php


namespace sinri\ark\web\psr\psr17;


use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use sinri\ark\web\psr\psr7\ArkWebRequest;

class ArkWebRequestFactory implements RequestFactoryInterface
{

    /**
     * Create a new request.
     *
     * @param string $method The HTTP method associated with the request.
     * @param UriInterface|string $uri The URI associated with the request. If
     *     the value is a string, the factory MUST create a UriInterface
     *     instance based on it.
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new ArkWebRequest($method,$uri);
    }
}