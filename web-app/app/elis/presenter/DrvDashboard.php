<?php

namespace elis\presenter;

use elis\model;

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
        $list = new model\CodeList("countries.json");
        $this->drvTmplt->setData('content', "Hello in driver and co-driver section."  .
            (new model\CodeList("event-types.json"))->legendToStr("Event types") .
            (new model\CodeList("countries.json"))->legendToStr("Countries"));
    }

    protected function table()
    {
    }
}
