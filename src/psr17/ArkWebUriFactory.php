<?php


namespace sinri\ark\web\psr\psr17;


use InvalidArgumentException;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use sinri\ark\web\psr\psr7\ArkWebUri;

class ArkWebUriFactory implements UriFactoryInterface
{

    /**
     * Create a new URI.
     *
     * @param string $uri
     *
     * @return UriInterface
     *
     * @throws InvalidArgumentException If the given URI cannot be parsed.
     */
    public function createUri(string $uri = ''): UriInterface
    {
        return ArkWebUri::fromUriString($uri);
    }
}