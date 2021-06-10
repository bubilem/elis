<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Event dispatcher administration presenter
 * @version 0.0.1 210610 created
 */
class DspEvent extends Dispatcher
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Event Administration');
    }


    protected function table()
    {
    }
}
