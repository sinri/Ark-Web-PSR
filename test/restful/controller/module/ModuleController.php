<?php


namespace sinri\ark\web\psr\test\restful\controller\module;


use sinri\ark\web\psr\psr7\ArkWebServerRequest;
use sinri\ark\web\psr\service\kit\ArkWebController;

class ModuleController extends ArkWebController
{
    /**
     * http://localhost/phpstorm/Ark-Web-PSR/test/restful/index.php/module/ModuleController/methodB?c=d&e=f#/h/i
     */
    public function methodB()
    {
        $this->getResponse()->appendToBody(__METHOD__ . ' sn: ' . $this->getRequest()->getAttribute(ArkWebServerRequest::ATTRIBUTE_REQUEST_SN));
    }
}