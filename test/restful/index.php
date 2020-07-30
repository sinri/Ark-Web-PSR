<?php

use sinri\ark\core\ArkLogger;
use sinri\ark\web\psr\service\ArkWebRouterForPSR;
use sinri\ark\web\psr\service\ArkWebServiceForPSR;
use sinri\ark\web\psr\service\kit\ArkWebRestfulRouteRule;
use sinri\ark\web\psr\test\restful\middleware\GatewayMiddleware;

require_once __DIR__ . '/../../vendor/autoload.php';

$logger = new ArkLogger(__DIR__ . '/../../log', 'restful-test');

$rule = (new ArkWebRestfulRouteRule(__DIR__ . '/controller', 'sinri\ark\web\psr\test\restful\controller'))
    ->setLogger($logger)
    ->addMiddlewareToQueue(GatewayMiddleware::class);

$router = new ArkWebRouterForPSR();
$router->setLogger($logger);
$router->addRouteRule($rule);

$service = new ArkWebServiceForPSR();
$service->setLogger($logger);
$service->setRouter($router);
$service->handle();
