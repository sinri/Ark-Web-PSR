<?php


namespace sinri\ark\web\psr\service;


use Exception;
use sinri\ark\core\ArkLoggerPropertyTrait;
use sinri\ark\web\psr\psr7\ArkWebResponse;
use sinri\ark\web\psr\psr7\ArkWebServerRequest;

class ArkWebServiceForPSR
{
    use ArkLoggerPropertyTrait;

    /**
     * @var ArkWebRouterForPSR
     */
    protected $router;

    /**
     * @return ArkWebRouterForPSR
     */
    public function getRouter(): ArkWebRouterForPSR
    {
        return $this->router;
    }

    /**
     * @param ArkWebRouterForPSR $router
     * @return ArkWebServiceForPSR
     */
    public function setRouter(ArkWebRouterForPSR $router): ArkWebServiceForPSR
    {
        $this->router = $router;
        return $this;
    }

    public function handle(){
        try {
            // 1. set up request
            $request=ArkWebServerRequest::autoCreateServerRequest();
            // 2. seek route rule
            $routeRule=$this->router->seekRuleForRequest($request);
            // 3. execute and make response
            $response = $routeRule->execute($request);
            // 4. respond to client
            ArkWebResponse::respond($response);
        } catch (Exception $e) {
            $this->respondJsonWhenError($e);
        }
    }

    /**
     * To Be Overrode if needed
     * @param Exception $e
     */
    protected function respondJsonWhenError(Exception $e){
        try {
            if ($e->getCode() === 0) {
                $code = 200;
            } else {
                $code = $e->getCode();
            }
            $response = (ArkWebResponse::makeResponse($code))
                ->writeAsJson([
                    'code' => 'FAIL',
                    'data' => [
                        'error_code' => $e->getCode(),
                        'error_message' => $e->getMessage(),
                    ]
                ]);
            ArkWebResponse::respond($response);
        }catch (Exception $exception){
            echo __METHOD__.'@'.__LINE__.' Exception: '.$exception->getMessage();
        }
    }
}