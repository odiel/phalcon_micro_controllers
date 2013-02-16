<?php

namespace Modules\Main\Assets;

class Js extends \Modules\Main\Assets
{

    public function get()
    {
        $_assetsList = array();
        for ($i = 0; $i < count($this->assetsList); $i++) {
            if (file_exists(PUBLIC_PATH.'js/'.$this->assetsList[$i])) {
                $_assetsList[] = '//js/'.$this->assetsList[$i];
            }
        }

        unset($this->assetsList);

        if (count($_assetsList) > 0) {
            $options = array(
                'files' => $_assetsList,
                'maxAge' =>  \Application::$config['assets']['maxAge'],
                'debug' => \Application::$config['assets']['debug'],
            );

            \Minify::serve('Files', $options);

            unset($options);
        }
    }
}