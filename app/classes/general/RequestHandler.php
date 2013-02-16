<?php

namespace Classes\General;

class RequestHandler
{
    public function __construct($application)
    {
        $this->_app = $application;
    }

    public function handle()
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
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
        }

        $count -= 1;

        if ($count == 1) {
            $module = $this->_defaultModule;
            $controller = $parts[0];
            $action = '';
        }

        if ($count == 2) {
            $module = $this->_defaultModule;
            $controller = $parts[0];
            $action = $parts[1];
        }

        if ($count > 2) {
            $module = $parts[0];
            $controller = $parts[1];
            $action = $parts[2];
        }

        $classString = false;

        $tryClass = '\\Modules\\'.$module.'\\'.$controller.'\\'.$action;

        if (class_exists($tryClass)) {
            $classString = $tryClass;
        } else {
            $tryClass = '\\Modules\\'.$module.'\\'.$controller;
            if (class_exists($tryClass)) {
                $classString = $tryClass;
            }
        }

        if ($classString !== false) {
            $method = strtolower($this->_app->request->getMethod());

            $class = new $classString();
            $class->setDI($this->_app->getDI());
            $class->initialize();

            if (method_exists($class, $method)) {
                $l = strlen($url['path']);

                if ($url['path'][$l-1] == '/') {
                    $url['path'] = substr($url['path'], 0, $l-1);
                }

                $this->_app->get($url['path'], array($class, $method));
            }
        }
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

    public function addAlias($url, $class, $method)
    {
        $this->_alias[$url] = array($class, $method);
    }

    private $_app = null;

    private $_defaultModule = 'index';
    private $_defaultController = 'index';
    private $_defaultAction = 'index';

    private $_alias = array();

}