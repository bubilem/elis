<?php

namespace elis\presenter;

/**
 * Error presenter
 * @version 0.0.1 201121 created
 */
class Error extends Main
{

    public function __construct(array $params)
    {
        parent::__construct($params);
    }

    public function run()
    {
        switch ($this->getParam(0)) {
            case '404':
                echo file_get_contents("app/elis/template/error/404.html");
                break;
            case '500':
            default:
                echo file_get_contents("app/elis/template/error/500.html");
        }
    }
}
