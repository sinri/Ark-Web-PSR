<?php


namespace sinri\ark\web\psr\psr17;


use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use sinri\ark\web\psr\psr7\ArkWebResponse;

class ArkWebResponseFactory implements ResponseFactoryInterface
{

    /**
     * Create a new response.
     *
     * @param int $code HTTP status code; defaults to 200
     * @param string $reasonPhrase Reason phrase to associate with status code
     *     in generated response; if none is provided implementations MAY use
     *     the defaults as suggested in the HTTP specification.
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return ArkWebResponse::makeResponse($code,$reasonPhrase);
    }
}