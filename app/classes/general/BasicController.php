<?php

namespace Classes\General;

class BasicController extends \Phalcon\DI\Injectable
{

    public function initialize()
    {}

    public function get()
    {}

    public function post()
    {}

    public function put()
    {}

    public function delete()
    {}


    public function renderView($controller, $action)
    {
        $response = new \Classes\General\ResponseHandler();
        $response->html($this->view->getRender($controller, $action));
    }

}