<?php

namespace elis\presenter;

use elis\utils;

/**
 * Error presenter
 * @version 0.0.1 201121 created
 */
class Error extends Main
{

    public function __construct(array $params)
    {
        parent::__construct($params);
    }

    public function run()
    {
        $tmplt = new utils\Template("error.html");
        $tmplt->setData('lang', utils\Conf::get("DEF_LANG"));
        $tmplt->setData('base', utils\Conf::get("URL_BASE") . utils\Conf::get("URL_DIR"));
        switch ($this->getParam(0)) {
            case '404':
                $tmplt->setData('code', '404');
                $tmplt->setData('name', 'Not Found');
                $tmplt->setData('message', 'The requested resource could not be found but may be available in the future. Subsequent requests by the client are permissible.');
                break;
            case '500':
            default:
                $tmplt->setData('code', '500');
                $tmplt->setData('name', 'Internal error');
                $tmplt->setData('message', 'Auuuuu.');
        }
        echo $tmplt->render();
    }
}
