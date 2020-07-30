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

    public function __construct(ArkWebResponse $response = null)
    {
        if ($response === null) {
            $response = ArkWebResponse::makeResponse(200, 'OK');
        }
        $this->response = $response;
    }

    /**
     * @param ArkWebResponse $response
     * @return ArkWebMiddleware
     */
    public function setResponse(ArkWebResponse $response): ArkWebMiddleware
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler If own handler exists, it might be null
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler = null): ResponseInterface
    {
        $this->request = $request;

        $this->handler = $this->getOwnHandler();
        if ($this->handler === null) {
            $this->handler = $handler;
        }
        if ($this->handler === null) {
            return $this->response;
        }

        if ($this->handler instanceof ArkWebRequestHandler) {
            $this->handler->setResponse($this->response);
        }

        return $this->handler->handle($this->request);
    }

    /**
     * @return ArkWebRequestHandler|null
     * By default it returns null, to use the provided handler.
     * If its own handler is available, the provided handler would be ignored.
     * So if you want to write a pure middleware with handler itself,
     * Override this class and this method.
     */
    protected function getOwnHandler()
    {
        return null;
    }

//    protected function handlerMethod(ServerRequestInterface $request,ArkWebResponse $response):ArkWebResponse{
//        $response->appendToBody(__METHOD__.'@'.__LINE__.PHP_EOL);
//        return $response;
//    }

    /**
     * @return bool
     */
    public function shouldFinishRequestHandleQueue()
    {
        return $this->response->isHandleFinished();
    }
}