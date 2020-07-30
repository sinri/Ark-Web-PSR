<?php


namespace sinri\ark\web\psr\service\kit;


use Psr\Http\Message\ServerRequestInterface;
use sinri\ark\core\ArkLoggerPropertyTrait;
use sinri\ark\web\psr\psr7\ArkWebResponse;

class ArkWebController
{
    use ArkLoggerPropertyTrait;

    /**
     * @var ServerRequestInterface
     */
    private $request;
    /**
     * @var ArkWebResponse
     */
    private $response;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return ArkWebResponse
     */
    public function getResponse(): ArkWebResponse
    {
        return $this->response;
    }
}