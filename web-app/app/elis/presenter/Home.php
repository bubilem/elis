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
        $this->pageTmplt->setData('title', 'European Logistic Information System');
        $this->pageTmplt->setData('main', $tmplt);
        echo $this->pageTmplt;
    }
}
