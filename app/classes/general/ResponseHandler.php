<?php

namespace Classes\General;

class ResponseHandler extends \Phalcon\Http\Response
{

    public function html($content)
    {
        $this->setStatusCode(200, 'OK');
        $this->setHeader('Content-Type', 'text/html');
        $this->setContent($content);
        $this->send();
    }

    public function text($content)
    {
        $this->setStatusCode(200, 'OK');
        $this->setHeader('Content-Type', 'text/plain');
        $this->setContent($content);
        $this->send();
    }

}