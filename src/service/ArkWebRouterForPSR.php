<?php


namespace sinri\ark\web\psr\service;


use Exception;
use sinri\ark\core\ArkLoggerPropertyTrait;
use sinri\ark\web\psr\psr7\ArkWebServerRequest;

class ArkWebRouterForPSR
{
    use ArkLoggerPropertyTrait;

    /**
     * @var ArkWebRouteRuleForPSR[]
     */
    protected $routeRuleList;

    /**
     * @param ArkWebServerRequest $request
     * @return ArkWebRouteRuleForPSR
     * @throws Exception No Matched Route Rule
     */
    public function seekRuleForRequest(ArkWebServerRequest $request):ArkWebRouteRuleForPSR{
        foreach ($this->routeRuleList as $routeRule){
            if($routeRule->isRequestMatchThisRule($request)) {
                return $routeRule;
            }
        }
        throw new Exception("Cannot find out the rule matched the request");
    }

    public function addRouteRule(ArkWebRouteRuleForPSR $routeRule){
        $this->routeRuleList[]=$routeRule;
        return $this;
    }
}