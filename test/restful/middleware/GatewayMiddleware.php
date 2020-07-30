<?php


namespace sinri\ark\web\psr\test\restful\middleware;


use Psr\Http\Message\ServerRequestInterface;
use sinri\ark\web\psr\psr7\ArkWebResponse;
use sinri\ark\web\psr\service\kit\ArkWebSelfHandleMiddleware;

class GatewayMiddleware extends ArkWebSelfHandleMiddleware
{
    protected function handlerMethod(ServerRequestInterface $request, ArkWebResponse $response): ArkWebResponse
    {
        $response->appendToBody(__METHOD__ . '@' . __LINE__ . PHP_EOL);
        return $response;
    }
}