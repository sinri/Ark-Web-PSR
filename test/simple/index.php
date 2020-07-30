<?php

use sinri\ark\web\psr\service\ArkWebRouterForPSR;
use sinri\ark\web\psr\service\ArkWebServiceForPSR;
use sinri\ark\web\psr\test\simple\SimpleRouteRuleA;

require_once __DIR__ . '/../../vendor/autoload.php';

$router = new ArkWebRouterForPSR();
$router->addRouteRule(new SimpleRouteRuleA());

$service = new ArkWebServiceForPSR();
$service->setRouter($router);
$service->handle();


// http://localhost/phpstorm/Ark-Web-PSR/test/simple/index.php/any?c=d&e=f#/h/i