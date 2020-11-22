<?php

namespace elis\presenter;

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
        echo file_get_contents("app/elis/template/page/home.html");
    }
}
