<?php

namespace elis\presenter;

use elis\utils;

/**
 * Dashboard presenter
 * @version 0.0.1 201223 created
 */
class Dashboard extends Main
{

    /**
     * Main page template
     *
     * @var utils\Template
     */
    protected $tmplt;

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->tmplt = new utils\Template("adm/administration.html");
        $this->tmplt->setData('lang', utils\Conf::get("DEF_LANG"));
        $this->tmplt->setData('base', utils\Conf::get("URL_BASE") . utils\Conf::get("URL_DIR"));
        $this->tmplt->setData('title', "Dashboard");
        $this->tmplt->setData('content', "Hello in ELIS administration.");
    }

    /**
     * Main run method
     *
     * @return void
     */
    public function run()
    {
        echo $this->tmplt;
    }
}
