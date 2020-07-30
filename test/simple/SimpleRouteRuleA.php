<?php


namespace sinri\ark\web\psr\test\simple;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use sinri\ark\web\psr\psr7\ArkWebResponse;
use sinri\ark\web\psr\psr7\ArkWebServerRequest;
use sinri\ark\web\psr\service\ArkWebRouteRuleForPSR;

class SimpleRouteRuleA extends ArkWebRouteRuleForPSR
{
    public function __construct()
    {
//        $handler=new ArkWebRequestHandler();
//        $handler->setHandleCallable(function(ServerRequestInterface $request,ArkWebResponse $response):ArkWebResponse{
//            $response->setStatus('200')
//                ->appendToBody("<pre>".__METHOD__.' '.__FUNCTION__.'@'.__LINE__);
//            return $response;
//        });
//        //$this->requestHandlerQueue=[$handler];
//
//        $handlerToDebug=new ArkWebRequestHandler();
//        $handlerToDebug->setHandleCallable([SimpleRouteRuleA::class,'handlerToDebugRequest']);
//        $this->requestHandlerQueue[]=$handlerToDebug;
    }

    public function isRequestMatchThisRule(ArkWebServerRequest $request): bool
    {
        if (preg_match('#/.+/simple/index.php/?(.+)?#', $request->getUri()->getPath(), $matched)) {
            return true;
        }
        return false;
    }

    protected function handlerToDebugRequest(ServerRequestInterface $request, ArkWebResponse $response): ArkWebResponse
    {
        $response
            ->appendToBody('<pre>' . PHP_EOL . __METHOD__)
            ->appendToBody(PHP_EOL . 'URI' . PHP_EOL)
            ->appendToBody($request->getUri()->getAuthority() . $request->getRequestTarget())
            ->appendToBody(PHP_EOL . 'METHOD' . PHP_EOL)
            ->appendToBody($request->getMethod())
            ->appendToBody(PHP_EOL . 'HEADERS' . PHP_EOL)
            ->appendToBody(var_export($request->getHeaders(), true))
            ->appendToBody(PHP_EOL . 'COOKIES' . PHP_EOL)
            ->appendToBody(var_export($request->getCookieParams(), true))
            ->appendToBody(PHP_EOL.'SERVER'.PHP_EOL)
            ->appendToBody(var_export($request->getServerParams(),true))
            ->appendToBody(PHP_EOL . 'QUERY' . PHP_EOL)
            ->appendToBody(var_export($request->getQueryParams(), true))
            ->appendToBody(PHP_EOL . 'BODY' . PHP_EOL)
            ->appendToBody(var_export($request->getBody()->__toString(), true))
            ->appendToBody(PHP_EOL . 'PARSED BODY' . PHP_EOL)
            ->appendToBody(var_export($request->getParsedBody(), true));
//         throw new \Exception("mmm");
        return $response;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handlerToDebugRequest($request, $this->response);
    }
}