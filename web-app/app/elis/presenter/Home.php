<?php

namespace elis\presenter;

use elis\utils;

/**
 * Home presenter
 * @version 0.0.1 201121 created
 */
class Home extends Main
{

    public function __construct(array $params)
    {
        parent::__construct($params);
    }

    public function run()
    {
        $tmplt = new utils\Template("page/home.html");
        $tmplt->setData('lang', utils\Conf::get("DEF_LANG"));
        $tmplt->setData('base', utils\Conf::get("URL_BASE") . utils\Conf::get("URL_DIR"));
        echo $tmplt;
    }
}
