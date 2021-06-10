<?php

namespace elis\presenter;

use elis\utils;

/**
 * Dashboard presenter
 * @version 0.0.1 201223 created
 */
class DspDashboard extends Dispatcher
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', "Dispatcher Dashboard");
        $this->dspTmplt->setData('content', "Hello in dispatcher administration.");
    }

    protected function table()
    {
    }
}
