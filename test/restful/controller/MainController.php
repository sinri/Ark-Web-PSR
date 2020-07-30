<?php


namespace sinri\ark\web\psr\test\restful\controller;


use sinri\ark\web\psr\service\kit\ArkWebController;

class MainController extends ArkWebController
{
    /**
     * @param string $p
     * http://localhost/phpstorm/Ark-Web-PSR/test/restful/index.php/MainController/methodA?c=d&e=f#/h/i
     */
    public function methodA($p = 'q')
    {
        $this->getResponse()->appendToBody(__METHOD__ . ' p: ' . $p);
    }
}