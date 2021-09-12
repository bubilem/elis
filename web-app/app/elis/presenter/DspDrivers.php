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


    protected function driver()
    {
        $messageTmplt = new utils\Template("other/message.html");
        $q = new db\Select();
        $q->setSelect("u.id, u.name, u.surname, COUNT(r.id) routecount")
            ->addSelect("SUM(r.mileage) summileage, SUM(p.weight) sumweight")
            ->setFrom("user u JOIN route_has_user rhu ON (u.id = " . intval($this->getParam(2)) . " AND rhu.user = u.id AND rhu.role = 'DRV')")
            ->addFrom("JOIN route r ON rhu.route = r.id")
            ->addFrom("LEFT JOIN event e ON e.type = 'UNL' AND e.route = r.id")
            ->addFrom("LEFT JOIN package_log pl ON pl.event = e.id")
            ->addFrom("LEFT JOIN package p ON pl.package = p.id")
            ->setGroup("u.id");
        $driver = $q->run();
        if (empty($driver[0])) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'User does not exist or has no data.'
            ]));
            $this->table();
            return;
        }
        $q->clear()
            ->setSelect("e.type, COUNT(e.id) count")
            ->setFrom("event e JOIN user u ON e.recorded = u.id")
            ->setWhere("u.id = " . $driver[0]['id'])
            ->setGroup("e.type");
        $tableRowTmplt = new utils\Template("dsp/drivers/driver-table-row.html");
        $rows = '';
        foreach ($q->run() as $record) {
            $tableRowTmplt->clearData()->setAllData([
                'event' => $record['type'],
                'total' => $record['count'],
                'perroute' => number_format($record['count'] / $driver[0]['routecount'], 3),
                'per250km' => number_format($record['count'] / $driver[0]['summileage'] * 250, 3),
                'per500km' => number_format($record['count'] / $driver[0]['summileage'] * 500, 3),
                'per1000km' => number_format($record['count'] / $driver[0]['summileage'] * 1000, 3)
            ]);
            $rows .= $tableRowTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/drivers/driver-table.html", [
            'caption' => $driver[0]['name'] . ' ' . $driver[0]['surname'],
            'totalroutes' => $driver[0]['routecount'],
            'totalmileage' => number_format($driver[0]['summileage'], 0, ',', ' '),
            'totalweight' => number_format($driver[0]['sumweight'], 0, ',', ' '),
            'rows' => $rows
        ]));
        if (empty($rows)) {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'std',
                'message' => 'There is no record in the database'
            ]));
        }
    }


    protected function table()
    {
        $tableRowTmplt = new utils\Template("dsp/drivers/table-row.html");
        $rows = '';
        $q = new db\Select();
        $q->setSelect("u.id, u.name, u.surname, COUNT(r.id) routecount, SUM(r.mileage) summileage");
        $q->addSelect("SUM(p.weight) sumweight");
        $q->setFrom("user u JOIN route_has_user rhu ON rhu.user = u.id AND rhu.role = 'DRV'");
        $q->addFrom("JOIN route r ON rhu.route = r.id");
        $q->addFrom("LEFT JOIN event e ON e.type = 'UNL' AND e.route = r.id");
        $q->addFrom("LEFT JOIN package_log pl ON pl.event = e.id");
        $q->addFrom("LEFT JOIN package p ON pl.package = p.id");
        $q->setGroup("u.id");
        $drivers = $q->run();
        $q->clear();
        $q->setSelect("u.id user, e.type, COUNT(e.id) count");
        $q->setFrom("event e JOIN user u ON e.recorded = u.id");
        $q->setGroup("u.id, e.type");
        $es = [];
        foreach ($q->run() as $record) {
            $event = '[' . $record['count'] . '&times;' . $record['type'] . '] ';
            if (empty($es[$record['user']])) {
                $es[$record['user']] = $event;
            } else {
                $es[$record['user']] .= $event;
            }
        }
        foreach ($drivers as $record) {
            $tableRowTmplt->clearData()->setAllData([
                'id' => $record['id'],
                'driver' => $record['surname'] . ' ' . $record['name'],
                'routecount' => $record['routecount'],
                'summileage' => number_format($record['summileage'], 0, ',', ' '),
                'sumweight' => number_format($record['sumweight'], 0),
                'events' => !empty($es[$record['id']]) ? $es[$record['id']] : ''
            ]);
            $rows .= $tableRowTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/drivers/table.html", [
            'caption' => 'Drivers',
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
