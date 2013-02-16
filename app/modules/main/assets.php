<?php

namespace Modules\Main;

class Assets extends \Classes\General\BasicController
{

    public $assetsList  = array();

    public function initialize()
    {
        $assets = $this->request->getQuery('r');
        if ($assets != null) {
            $this->assetsList = explode(',', $assets);
        }

        set_include_path(LIB_PATH.'Minify/'.PATH_SEPARATOR.get_include_path());

        \Minify::setCache(new \Minify_Cache_APC());
    }

}
