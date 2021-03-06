<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Test presenter for development testing only
 * @version 0.0.1 201121 created
 */
class Test extends Main
{

    public function __construct(array $params)
    {
        parent::__construct($params);
    }

    public function run()
    {
        var_dump(model\Main::dbToModelName('avg-consuption-ext'));
        var_dump(model\Main::modelToDbName('avgConsuptionExt'));

        /*
        echo "<h2>DML queries testing</h2>";
        echo new db\Insert('user', ['name' => 'Michal', 'surname' => 'Bubílek']);
        echo "<br>";
        echo new db\Update('user', ['name' => 'Michal', 'surname' => 'Bubílek'], 5);
        echo "<br>";
        echo new db\Delete('user', 5);
        echo "<br>";
        $q = new db\Select();
        $q->setSelect("*")->setFrom("user")->setWhere("id = 5");
        echo $q;
        var_dump($q->run());

        echo "<h2>Load existing user</h2>";
        $u = new model\User(1);
        $u->setPassword(utils\Secure::randHexaString());
        var_dump($u->save());
        var_dump($u, $u->getId());

        echo "<h2>New user</h2>";
        $v = new model\User();
        $v->setEmail("john@doe.com")->setPassword(utils\Secure::randHexaString());
        $v->setName("John")->setSurname("Doe");
        var_dump($v->save());
        var_dump($v, $v->getId());

        echo "<h2>Del user</h2>";
        $v = new model\User(24);
        var_dump($v->delete());
        var_dump($v, $v->getId());
        */
    }
}
