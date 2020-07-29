<?php


namespace sinri\ark\web\psr\service;


use sinri\ark\web\psr\psr7\ArkWebRequest;
use sinri\ark\web\psr\psr7\ArkWebServerRequest;

class ArkWebRouterForPSR
{
    /**
     * @var ArkWebRouteRuleForPSR[]
     */
    protected $routeRuleList;

    /**
     * @param ArkWebServerRequest $request
     * @return ArkWebRouteRuleForPSR
     * @throws \Exception No Matched Route Rule
     */
    public function seekRuleForRequest(ArkWebServerRequest $request):ArkWebRouteRuleForPSR{
        foreach ($this->routeRuleList as $routeRule){
            if($routeRule->isRequestMatchThisRule($request)) {
                return $routeRule;
            }
        }
        throw new \Exception("Cannot find out the rule matched the request");
    }

    public function addRouteRule(ArkWebRouteRuleForPSR $routeRule){
        $this->routeRuleList[]=$routeRule;
        return $this;
    }
}