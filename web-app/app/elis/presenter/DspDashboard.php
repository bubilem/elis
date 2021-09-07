<?php

namespace elis\presenter;

use elis\model;

/**
 * Dispatcher dashboard presenter
 * @version 0.1.2 210615 created
 */
class DspDashboard extends Dispatcher
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', "Dispatcher :: Dashboard");
        $this->dspTmplt->setData(
            'content',
            "Hello in dispatcher administration." .
                (new model\CodeList("event-types.json"))->legendToStr("Event types") .
                (new model\CodeList("package-states.json"))->legendToStr("Package states") .
                (new model\CodeList("package-types.json"))->legendToStr("Package types") .
                (new model\CodeList("countries.json"))->legendToStr("Countries")
        );
    }

    protected function table()
    {
    }
}
