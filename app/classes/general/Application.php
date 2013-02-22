<?php

namespace Classes\General;

class Application extends \Phalcon\Mvc\Micro
{
    static public $config = array();


    public function start()
    {
        $url = parse_url(substr($_SERVER['REQUEST_URI'], 1));
        $parts = explode('/', $url['path']);

        $module = $this->_defaultModule;
        $controller = $this->_defaultController;
        $action = $this->_defaultAction;
        $method = 'get';

        if ($parts[0] == ''){
            array_shift($parts);
        }

        $count = count($parts);

        if ($parts[$count-1] == ''){
            array_pop($parts);
            $count -= 1;
        }

        if ($count == 1) {
            $module = $this->_defaultModule;
            $controller = $parts[0];
            $action = '';
        }

        if ($count == 2) {
            $module = $parts[0];
            $controller = $parts[1];
            $action = '';
        }

        if ($count > 2) {
            $module = $parts[0];
            $controller = $parts[1];
            $action = $parts[2];
        }

        $classString = false;

        if (isset($this->_alias[$url['path']])) {
            $alias = $this->_alias[$url['path']];
            $module = $alias['module'];
            $controller = $alias['controller'];
            $action = $alias['action'];
            $method = $alias['method'];
        }

        //Round #1: using all $module, $controller and $action
        $tryClass = '\\Modules\\'.$module.'\\'.$controller.'\\'.$action;
        if (class_exists($tryClass)) {
            $classString = $tryClass;
        }

        //Round #2: using all $module and $controller
        if ($classString === false) {
            $tryClass = '\\Modules\\'.$module.'\\'.$controller;

            if (class_exists($tryClass)) {
                $classString = $tryClass;
            }
        }

        if ($classString !== false) {
            $method = strtolower($this->request->getMethod());

            $class = new $classString();

            if (method_exists($class, $method)) {
                $class->setDI($this->getDI());
                $class->initialize();

                $l = strlen($url['path']);

                if ($url['path'][$l-1] == '/') {
                    $url['path'] = substr($url['path'], 0, $l-1);
                }

                $this->get('/'.$url['path'], array($class, $method));
            }
        }

        $this->handle();
    }


    public function setDefaultModule($module)
    {
        $this->_defaultModule = $module;
    }

    public function setDefaultController($controller)
    {
        $this->_defaultController = $controller;
    }

    public function setDefaultAction($action)
    {
        $this->_defaultAction = $action;
    }

    public function addAlias($url, $module, $controller, $action, $method = 'GET')
    {
        $this->_alias[$url] = array(
            'module'        => $module,
            'controller'    => $controller,
            'action'        => $action,
            'method'        => $method
        );
    }

    private $_defaultModule = 'index';
    private $_defaultController = 'index';
    private $_defaultAction = 'index';

    private $_alias = array();

}