<?php
require_once __DIR__.'/../../vendor/autoload.php';

$router=new \sinri\ark\web\psr\service\ArkWebRouterForPSR();
$router->addRouteRule(new \sinri\ark\web\psr\test\simple\SimpleRouteRuleA());

$service=new \sinri\ark\web\psr\service\ArkWebServiceForPSR();
$service->setRouter($router);
$service->handle();

