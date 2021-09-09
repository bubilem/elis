<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Drivers statistics presenter
 
 * @version 0.2.3 210909 created
 */
class DspDrivers extends Dispatcher
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Dispatcher :: Driver statistics');
    }


    protected function table()
    {
        $tableRowTmplt = new utils\Template("dsp/drivers/table-row.html");
        $rows = '';


        $q = new db\Select();
        $q->setSelect("u.name, u.surname, COUNT(r.id) routecount, SUM(r.mileage) summileage");
        $q->setFrom("user u JOIN route_has_user rhu ON rhu.user = u.id AND rhu.role = 'DRV'");
        $q->addFrom("JOIN route r ON rhu.route = r.id");
        $q->setGroup("u.id");


        foreach ($q->run() as $record) {
            $tableRowTmplt->clearData()->setAllData([
                'driver' => $record['surname'] . ' ' . $record['name'],
                'routecount' => $record['routecount'],
                'summileage' => $record['summileage']
            ]);
            $rows .= $tableRowTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/drivers/table.html", [
            'caption' => 'Drivers statistics',
            'rows' => $rows
        ]));
        if (empty($rows)) {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'std',
                'message' => 'There is no record in the database'
            ]));
        }
    }
}
