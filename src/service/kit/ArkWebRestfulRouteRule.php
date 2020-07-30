<?php


namespace sinri\ark\web\psr\service\kit;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use sinri\ark\web\psr\psr7\ArkWebResponse;
use sinri\ark\web\psr\psr7\ArkWebServerRequest;
use sinri\ark\web\psr\service\ArkWebRouteRuleForPSR;

class ArkWebRestfulRouteRule extends ArkWebRouteRuleForPSR
{
    use ArkWebRestfulPathTrait;

    public function __construct(string $rootPath, string $rootNamespace)
    {
        $this->bind($rootPath, $rootNamespace);
    }

    public function isRequestMatchThisRule(ArkWebServerRequest $request): bool
    {
        return false !== $this->seekControllerAndMethod();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->response === null) {
            $this->response = ArkWebResponse::makeResponse(200);
        }

        $closure = $this->seekControllerAndMethod();
        call_user_func_array($closure, [$request, $this->response]);
        return $this->response;
    }

}