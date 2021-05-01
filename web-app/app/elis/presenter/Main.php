<?php

namespace elis\presenter;

use elis\utils;

/**
 * Main presenter
 * @version 0.0.1 201121 created
 */
abstract class Main
{

    /**
     * Uri params
     *
     * @var array
     */
    protected $params;

    /**
     * Page Main Temaplate
     *
     * @param utils\Template $pageTmplt
     */
    protected $pageTmplt;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
        $this->pageTmplt = new utils\Template("page.html");
        $this->pageTmplt->setData('lang', utils\Conf::get("DEF_LANG"));
        $this->pageTmplt->setData('base', utils\Conf::get("URL_BASE") . utils\Conf::get("URL_DIR"));
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (utils\db\MySQL::isConnected()) {
            utils\db\MySQL::close();
        }
    }

    /**
     * Get param value by index
     *
     * @param int $index
     * @return mixed string on success, otherwise false
     */
    public function getParam(int $index)
    {
        return isset($this->params[$index]) ? $this->params[$index] : false;
    }
}
