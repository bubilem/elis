<?php

namespace elis\presenter;

use elis\utils;

/**
 * Driver dashboard presenter
 * @version 0.1.3 210613 created
 */
class DrvDashboard extends Driver
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', "Driver :: Dashboard");
        $this->drvTmplt->setData('content', "Hello in driver and co-driver section.");
    }

    protected function table()
    {
    }
}
