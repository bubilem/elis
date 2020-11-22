<?php

namespace elis\presenter;

/**
 * Test presenter for development testing only
 * @version 0.0.1 201121 created
 */
class Test extends Main
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        var_dump("Test::constructor");
    }

    public function run()
    {
        var_dump("Test::run");
        var_dump($this->params);
    }
}
