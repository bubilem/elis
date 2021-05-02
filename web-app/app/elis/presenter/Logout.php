<?php

namespace elis\presenter;

use elis\controller\Router;


/**
 * Logout presenter
 * @version 0.0.1 210502 created
 */
class Logout extends Main
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        Router::redirect("");
    }

    public function run()
    {
    }
}
