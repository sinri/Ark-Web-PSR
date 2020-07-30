<?php


namespace sinri\ark\web\psr\service;


use sinri\ark\core\ArkLoggerPropertyTrait;
use sinri\ark\web\psr\psr15\ArkWebRequestHandler;
use sinri\ark\web\psr\psr17\ArkWebResponseFactory;
use sinri\ark\web\psr\psr7\ArkWebServerRequest;

abstract class ArkWebRouteRuleForPSR extends ArkWebRequestHandler
{
    use ArkLoggerPropertyTrait;

    /**
     * @var string[] each as extended class of `ArkWebMiddleware`
     */
    protected $middlewareClassQueue = [];
    /**
     * @var ArkWebRequestHandler[]
     */
//    protected $requestHandlerQueue=[];

    abstract public function isRequestMatchThisRule(ArkWebServerRequest $request): bool;

//    public function addRequestHandlerToQueue(ArkWebRequestHandler $handler){
//        $this->requestHandlerQueue[]=$handler;
//        return $this;
//    }

    /**
     * @param string $middleware `Extended_ArkWebMiddleware::class`
     * @return $this
     */
    public function addMiddlewareToQueue(string $middleware)
    {
        $this->middlewareClassQueue[] = $middleware;
        return $this;
    }

    public function execute(ArkWebServerRequest $request)
    {
        $this->response = (new ArkWebResponseFactory())->createResponse('200');
        // middleware: would be executed by order and would stop halfway
        foreach ($this->middlewareClassQueue as $middlewareClassName) {
            $middleware = new $middlewareClassName($this->response);
            $this->response = $middleware->process($request);// todo cannot be null
            if ($middleware->shouldFinishRequestHandleQueue()) {
                return $this->response;
            }
        }
        // prepare handler: would be executed by order and would stop halfway
//        foreach ($this->requestHandlerQueue as $requestHandler){
//            $response=$requestHandler->setResponse($response)->handle($request);
//            if($requestHandler->shouldFinishRequestHandleQueue()){
//                return $response;
//            }
//        }
        // It not stopped, execute the handling code itself
        return $this->setResponse($this->response)->handle($request);
    }
}