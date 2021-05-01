<?php

namespace elis\presenter;

use elis\utils;

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
        $tmplt = new utils\Template("page/error.html");
        switch ($this->getParam(0)) {
            case '404':
                $this->pageTmplt->setData('title', '404 Not Found');
                $tmplt->setData('message', 'The requested resource could not be found but may be available in the future. Subsequent requests by the client are permissible.');
                break;
            case '500':
            default:
                $this->pageTmplt->setData('title', '500 Internal error');
                $tmplt->setData('message', 'Auuuuu.');
        }
        $this->pageTmplt->setData('main', $tmplt);
        echo $this->pageTmplt;
    }
}
