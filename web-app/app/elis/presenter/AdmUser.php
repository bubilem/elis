<?php

namespace elis\presenter;

use elis\utils\db;
use elis\model;

/**
 * User administration presenter
 * @version 0.0.1 201223 created
 */
class AdmUser extends Administration
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->tmplt->setData('title', 'User administration');
    }

    public function run()
    {
        $this->show();
        echo $this->tmplt->render();
    }

    private function show()
    {
        $content = '';
        $query = (new db\Select())->setSelect("*")->setFrom("user")->setOrder('surname,name');
        var_dump($query->run());
        $this->tmplt->setData('content', $content);
    }
}
