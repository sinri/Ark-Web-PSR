<?php


namespace sinri\ark\web\psr\service;


use sinri\ark\web\psr\psr15\ArkWebRequestHandler;
use sinri\ark\web\psr\psr17\ArkWebResponseFactory;
use sinri\ark\web\psr\psr7\ArkWebRequest;
use sinri\ark\web\psr\psr7\ArkWebServerRequest;

abstract class ArkWebRouteRuleForPSR
{
    /**
     * @var ArkWebRequestHandler[]
     */
    protected $requestHandlerQueue=[];

    abstract public function isRequestMatchThisRule(ArkWebServerRequest $request): bool;

    public function addRequestHandlerToQueue(ArkWebRequestHandler $handler){
        $this->requestHandlerQueue[]=$handler;
        return $this;
    }

    public function executeWithHandlerQueue(ArkWebServerRequest $request){
        $response=(new ArkWebResponseFactory())->createResponse('200');
        foreach ($this->requestHandlerQueue as $requestHandler){
            $response=$requestHandler->setResponse($response)->handle($request);
            if($requestHandler->shouldFinishRequestHandleQueue()){
                break;
            }
        }
        return $response;
    }
}