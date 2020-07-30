<?php


namespace sinri\ark\web\psr\service\kit;


use Psr\Http\Message\ServerRequestInterface;
use sinri\ark\web\psr\psr15\ArkWebMiddleware;
use sinri\ark\web\psr\psr15\ArkWebRequestHandler;
use sinri\ark\web\psr\psr7\ArkWebResponse;

abstract class ArkWebSelfHandleMiddleware extends ArkWebMiddleware
{
    public function __construct(ArkWebResponse $response = null)
    {
        parent::__construct($response);
    }

    protected function getOwnHandler()
    {
        return (new ArkWebRequestHandler())
            ->setHandleCallableWithClosure(
                function (ServerRequestInterface $request, ArkWebResponse $response) {
                    return $this->handlerMethod($request, $response);
                }
            );
    }

    abstract protected function handlerMethod(ServerRequestInterface $request, ArkWebResponse $response): ArkWebResponse;

}