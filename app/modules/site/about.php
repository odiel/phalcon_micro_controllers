<?php

namespace Modules\Site;

class About extends \Classes\General\BasicController
{

    public function get()
    {
        $this->initialize();
        $this->view->setVar('title', 'test');
        $this->renderView('site', 'about');
    }

}