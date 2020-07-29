<?php


namespace sinri\ark\web\psr\psr15;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use sinri\ark\web\psr\psr7\ArkWebRequest;
use sinri\ark\web\psr\psr7\ArkWebResponse;

class ArkWebMiddleware implements MiddlewareInterface
{
    /**
     * @var ArkWebRequest
     */
    protected $request;
    /**
     * @var ArkWebRequestHandler
     */
    protected $handler;
    /**
     * @var ArkWebResponse
     */
    protected $response;

    public function __construct(ArkWebResponse $response=null)
    {
        if($response===null){
            $response=ArkWebResponse::makeResponse(200,'OK');
        }
        $this->response=$response;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->request=$request;
        $this->handler=$handler;

        $this->prepare();

        return $handler->handle($this->request);
    }

    protected function prepare(){
        if($this->handler instanceof ArkWebRequestHandler) {
            $this->handler->setResponse($this->response);
        }
    }
}