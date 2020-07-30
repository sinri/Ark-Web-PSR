<?php


namespace sinri\ark\web\psr\service\kit;


use Closure;
use ReflectionClass;
use ReflectionException;

trait ArkWebRestfulPathTrait
{
    protected $rootNamespace;
    protected $rootPath;
    protected $gateway = 'index.php';

    protected function bind(string $rootPath, string $rootNamespace)
    {
        $this->rootPath = $rootPath;
        $this->rootNamespace = $rootNamespace;
        return $this;
    }

    /**
     * @return bool|Closure
     */
    protected function seekControllerAndMethod()
    {
        $fullPathString = $this->fetchControllerPathString();
        $tmp = explode('?', $fullPathString);
        $pathString = isset($tmp[0]) ? $tmp[0] : '';
        $pathString = preg_replace('#^/#', '', $pathString);
//        var_dump($pathString);
        $components = explode("/", $pathString);
        $controllerClassName = $this->rootNamespace;// initialize, if it is a class
        $method = null;
        $params = [];
        foreach ($components as $index => $component) {
//            echo ($controllerClassName.'::'.$component).PHP_EOL;
            if (class_exists($controllerClassName)) {
//                echo 'controller Class Exists'.PHP_EOL;
                if (method_exists($controllerClassName, $component)) {
//                    echo 'Method Exists'.PHP_EOL;
                    $parametersAreCorrect = false;
                    try {
                        // confirm method
                        $reflector = new ReflectionClass($controllerClassName);
                        $foundMethod = $reflector->getMethod($component);

                        // parameters
                        //$parameters = array_slice($components, $index);
//                        echo 'getNumberOfRequiredParameters: '.$foundMethod->getNumberOfRequiredParameters().PHP_EOL;
//                        echo 'total: '.count($components).' index: '.$index.PHP_EOL;
                        if ($foundMethod->getNumberOfRequiredParameters() < count($components) - $index) {
                            $method = $component;
                            $params = array_slice($components, $index + 1);
                            $parametersAreCorrect = true;
                        }
                    } catch (ReflectionException $e) {
//                        echo 'ReflectionException: '.$e->getMessage().PHP_EOL;
                    }
                    if ($parametersAreCorrect) {
                        return function ($request, $response) use ($params, $method, $controllerClassName) {
                            $controller = new $controllerClassName($request, $response);
                            return call_user_func_array([$controller, $method], $params);
                        };
                    }
                }
            }
            $controllerClassName .= '\\' . $component;
        }
        return false;
    }

    protected function fetchControllerPathString()
    {
//        var_dump($_SERVER);
        $prefix = $_SERVER['SCRIPT_NAME'];
        //$delta=10;//original
        $delta = strlen($this->gateway) + 1;

        if (
            (strpos($_SERVER['REQUEST_URI'], $prefix) !== 0)
            && (strrpos($prefix, '/' . $this->gateway) + $delta == strlen($prefix))
        ) {
            $prefix = substr($prefix, 0, strlen($prefix) - $delta);
        }

        return substr($_SERVER['REQUEST_URI'], strlen($prefix));
    }
}