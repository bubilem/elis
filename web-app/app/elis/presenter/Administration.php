<?php

namespace elis\presenter;

use elis\utils;

/**
 * Main administration presenter
 * @version 0.0.1 201223 created
 */
class Administration extends Main
{

    protected $tmplt;

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->tmplt = new utils\Template("adm/administration.html");
        $this->tmplt->setData('lang', utils\Conf::get("DEF_LANG"));
        $this->tmplt->setData('base', utils\Conf::get("URL_BASE") . utils\Conf::get("URL_DIR"));
    }

    public function run()
    {
    }
}
