<?php


namespace sinri\ark\web\psr\psr15;


use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use sinri\ark\web\psr\psr7\ArkWebResponse;

class ArkWebRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ArkWebResponse
     */
    protected $response;
    /**
     * @var callable|array function(ServerRequestInterface $request,ArkWebResponse $response):ArkWebResponse
     */
    protected $handleCallable;

    /**
     * @param callable $handleCallable support pure callable (closure) and class-method array
     * @return ArkWebRequestHandler
     */
    public function setHandleCallable($handleCallable): ArkWebRequestHandler
    {
        $this->handleCallable = $handleCallable;
        return $this;
    }

    public function setHandleCallableWithClosure(Closure $handleCallable): ArkWebRequestHandler
    {
        $this->handleCallable = $handleCallable;
        return $this;
    }

    public function setHandleCallableWithClassAndMethod(string $className, string $method)
    {
        $this->handleCallable = [$className, $method];
        return $this;
    }

    /**
     * @param ResponseInterface $response
     * @return ArkWebRequestHandler
     */
    public function setResponse(ResponseInterface $response): ArkWebRequestHandler
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->response === null) {
            $this->response = ArkWebResponse::makeResponse(200);
        }

        if(!is_array($this->handleCallable)){
            $actualCallable=$this->handleCallable;
        }else {
            $className=$this->handleCallable[0];
            $actualCallable = [new $className,$this->handleCallable[1]];
        }
        if (!is_callable($actualCallable)) {
            $this->response->setStatus('500', 'No handler given');
        } else {
            $this->response = call_user_func_array($this->handleCallable, [$request, $this->response]);
        }
        return $this->response;
    }

    /**
     * @return bool
     */
    public function shouldFinishRequestHandleQueue(){
        return $this->response->isHandleFinished();
    }
}