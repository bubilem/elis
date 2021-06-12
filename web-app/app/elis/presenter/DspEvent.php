<?php

namespace elis\presenter;

use elis\model;
use elis\model\CodeList;
use elis\model\CodeListItem;
use elis\model\Event;
use elis\utils;
use elis\utils\db;

/**
 * Event dispatcher administration presenter
 * @version 0.0.1 210610 created
 */
class DspEvent extends Dispatcher
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Event Administration');
    }

    public function lodForm()
    {
        $route = new model\Route($this->getParam(2));
        if (!$route->getId()) {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Route does not exist.'
            ]));
            $this->table();
            return;
        }
        $formTmplt =  new utils\Template("dsp/event/lod-form.html", [
            'caption' => ' Route ' . $route . ' - Loading packages ',
            'route' => $route->getid()
        ]);
        $options = '';
        $result = (new db\Select())
            ->setSelect("p.id, p.code, p.type, SUBSTRING_INDEX(GROUP_CONCAT(l.state ORDER BY l.date DESC),',',1) laststate")
            ->setFrom("package p JOIN package_log l ON p.id = l.package")
            ->setHaving("laststate = 'WTG'")
            ->setGroup("p.id")
            ->run();
        if (empty($result)) {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'war',
                'message' => 'Nothing to load, no waiting packages.'
            ]));
            $this->table();
            return;
        }
        $checkboxTmplt = new utils\Template("other/checkbox.html");
        foreach ($result as $item) {
            $checkboxTmplt->clearData()->setAllData([
                'name' => 'pck',
                'id' => $item['id'],
                'label' => $item['code'],
                'checked' => ''
            ]);
            $options .= $checkboxTmplt;
        }
        $formTmplt->setData('packages', $options);
        $options = '';
        $result = (new db\Select())->setSelect("id, name, code")->setFrom("place")->setWhere("code LIKE 'DP-%'")->run();
        if (!empty($result)) {
            $checkboxTmplt = new utils\Template("other/radio.html");
            foreach ($result as $item) {
                $checkboxTmplt->clearData()->setAllData([
                    'name' => 'place',
                    'id' => $item['id'],
                    'label' => $item['code'] . ', ' . $item['name'],
                    'checked' => ''
                ]);
                $options .= $checkboxTmplt;
            }
        }
        $formTmplt->setData('places', $options ? $options : 'Ups, no place.');
        $this->dspTmplt->addData('content', (string)$formTmplt);
    }

    public function lod()
    {
        $messageTmplt = new utils\Template("other/message.html");
        $route = new model\Route(filter_input(INPUT_POST, 'route', FILTER_SANITIZE_NUMBER_INT));
        if (!$route->getId()) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'Route does not exist.'
            ]));
            $this->table();
            return;
        }
        $pcks = filter_input(INPUT_POST, 'pck', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (empty($pcks)) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'Mo packages to load.'
            ]));
            $this->table();
            return;
        }
        $ev = new Event("LOD");
        $ev->setRecorded($this->user->getId());
        $ev->setRoute($route->getId());
        $place = filter_input(INPUT_POST, 'place', FILTER_SANITIZE_NUMBER_INT);
        if ($place) {
            $ev->setPlace($place);
        }
        $ev->setPlaceManual(filter_input(INPUT_POST, 'place_manual', FILTER_SANITIZE_STRING));
        $ev->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        if ($ev->save()) {
            $messageTmplt->setData('message', 'Event ' . $ev . ' has been created.');
            $messageTmplt->setData('type', 'suc');
            foreach ($pcks as $id => $val) {
                $pck = new model\Package($id);
                if ($pck->getId()) {
                    $pck->createLog('TRN', $ev)->save();
                }
            }
        } else {
            $messageTmplt->setData('message', 'Event ' . $ev . ' has not been created.');
            $messageTmplt->setData('type', 'err');
        }
        $this->dspTmplt->addData('content', $messageTmplt);
        $this->table();
    }

    public function unlForm()
    {
        $route = new model\Route($this->getParam(2));
        if (!$route->getId()) {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Route does not exist.'
            ]));
            $this->table();
            return;
        }
        $formTmplt =  new utils\Template("dsp/event/unl-form.html", [
            'caption' => ' Route ' . $route . ' - Unloading packages ',
            'route' => $route->getid()
        ]);
        $options = '';
        $result = (new db\Select())
            ->setSelect("p.id, p.code, p.type, SUBSTRING_INDEX(GROUP_CONCAT(l.state ORDER BY l.date DESC),',',1) laststate")
            ->setFrom("package p JOIN package_log l ON p.id = l.package JOIN event e ON e.id = l.event")
            ->setWhere("e.route = " . $route->getId())
            ->setGroup("p.id")
            ->setHaving("laststate NOT IN('WTG','ACP','DST','FRW')")
            ->run();
        if (empty($result)) {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'war',
                'message' => 'Nothing to load, no waiting packages.'
            ]));
            $this->table();
            return;
        }
        $checkboxTmplt = new utils\Template("other/checkbox.html");
        foreach ($result as $item) {
            $checkboxTmplt->clearData()->setAllData([
                'name' => 'pck',
                'id' => $item['id'],
                'label' => $item['code'],
                'checked' => ''
            ]);
            $options .= $checkboxTmplt;
        }
        $formTmplt->setData('packages', $options);
        $options = '';
        $result = (new db\Select())->setSelect("id, name, code")->setFrom("place")->setWhere("code LIKE 'DP-%'")->run();
        if (!empty($result)) {
            $checkboxTmplt = new utils\Template("other/radio.html");
            foreach ($result as $item) {
                $checkboxTmplt->clearData()->setAllData([
                    'name' => 'place',
                    'id' => $item['id'],
                    'label' => $item['code'] . ', ' . $item['name'],
                    'checked' => ''
                ]);
                $options .= $checkboxTmplt;
            }
        }
        $formTmplt->setData('places', $options ? $options : 'Ups, no place.');
        $this->dspTmplt->addData('content', (string)$formTmplt);
    }

    public function unl()
    {
        $messageTmplt = new utils\Template("other/message.html");
        $route = new model\Route(filter_input(INPUT_POST, 'route', FILTER_SANITIZE_NUMBER_INT));
        if (!$route->getId()) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'Route does not exist.'
            ]));
            $this->table();
            return;
        }
        $pcks = filter_input(INPUT_POST, 'pck', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (empty($pcks)) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'No packages to unload.'
            ]));
            $this->table();
            return;
        }
        $ev = new Event("UNL");
        $ev->setRecorded($this->user->getId());
        $ev->setRoute($route->getId());
        $place = filter_input(INPUT_POST, 'place', FILTER_SANITIZE_NUMBER_INT);
        if ($place) {
            $ev->setPlace($place);
        }
        $ev->setPlaceManual(filter_input(INPUT_POST, 'place_manual', FILTER_SANITIZE_STRING));
        $ev->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        if ($ev->save()) {
            $messageTmplt->setData('message', 'Event ' . $ev . ' has been created.');
            $messageTmplt->setData('type', 'suc');
            foreach ($pcks as $id => $val) {
                $pck = new model\Package($id);
                if ($pck->getId()) {
                    $pck->createLog('WTG', $ev)->save();
                }
            }
        } else {
            $messageTmplt->setData('message', 'Event ' . $ev . ' has not been created.');
            $messageTmplt->setData('type', 'err');
        }
        $this->dspTmplt->addData('content', $messageTmplt);
        $this->table();
    }

    public function event()
    {
        $messageTmplt = new utils\Template("other/message.html");
        $eventType = strtoupper($this->getParam(2));
        $eventTypes = new CodeList('event-types.json');
        if (!($eventTypes->getItem($eventType) instanceof CodeListItem)) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'Event type does not exist.'
            ]));
            $this->table();
            return;
        }
        $route = new model\Route($this->getParam(3));
        if (!$route->getId()) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'Route does not exist.'
            ]));
            $this->table();
            return;
        }
        if (filter_input(INPUT_POST, 'route', FILTER_SANITIZE_NUMBER_INT)) {
            if (filter_input(INPUT_POST, 'route', FILTER_SANITIZE_NUMBER_INT) == $route->getId()) {
                $ev = new Event($eventType);
                $ev->setRecorded($this->user->getId());
                $ev->setRoute($route->getId());
                $place = filter_input(INPUT_POST, 'place', FILTER_SANITIZE_NUMBER_INT);
                if ($place) {
                    $ev->setPlace($place);
                }
                $ev->setPlaceManual(filter_input(INPUT_POST, 'place_manual', FILTER_SANITIZE_STRING));
                $ev->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
                if ($ev->save()) {
                    $messageTmplt->setData('message', 'Event ' . $ev . ' has been created.');
                    $messageTmplt->setData('type', 'suc');
                } else {
                    $messageTmplt->setData('message', 'Event ' . $ev . ' has not been created.');
                    $messageTmplt->setData('type', 'err');
                }
                $this->dspTmplt->addData('content', $messageTmplt);
                $this->table();
                return;
            } else {
                $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                    'type' => 'war',
                    'message' => 'No no no.'
                ]));
            }
        }
        $formTmplt =  new utils\Template("dsp/event/form.html", [
            'caption' => ' Route ' . $route . ' - ' . $eventType . ' - ' . ucfirst($eventTypes->getItem($eventType)->getName()),
            'eventType' =>  strtolower($eventType),
            'route' => $route->getId()
        ]);
        $options = '';
        $result = (new db\Select())->setSelect("id, name, code")->setFrom("place")->run();
        if (!empty($result)) {
            $checkboxTmplt = new utils\Template("other/radio.html");
            foreach ($result as $item) {
                $checkboxTmplt->clearData()->setAllData([
                    'name' => 'place',
                    'id' => $item['id'],
                    'label' => $item['code'] . ', ' . $item['name'],
                    'checked' => ''
                ]);
                $options .= $checkboxTmplt;
            }
        }
        $formTmplt->setData('places', $options ? $options : 'Ups, no place.');
        $this->dspTmplt->addData('content', (string)$formTmplt);
    }

    public function log()
    {
        $messageTmplt = new utils\Template("other/message.html");
        $route = new model\Route($this->getParam(2));
        if (!$route->getId()) {
            $messageTmplt->setAllData([
                'message' => "Route does not exist.",
                'type' => 'err'
            ]);
            $this->dspTmplt->setData('content', $messageTmplt);
            $this->table();
        }
        $tableRowTmplt = new utils\Template("dsp/event/event-table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("e.*")
            ->setFrom("event e")
            ->setWhere("e.route = " . $route->getId())
            ->setOrder('e.date DESC');
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'date' => $record['date'],
                    'type' => $record['type']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/event/event-table.html", [
            'caption' => "Package $route log",
            'rows' => $rows
        ]));
        if (empty($rows)) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'std',
                'message' => 'There is no record in the database'
            ]));
        }
    }

    protected function table()
    {
        $tableRowTmplt = new utils\Template("dsp/event/route-table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("r.*, SUBSTRING_INDEX(GROUP_CONCAT(e.type ORDER BY e.date DESC),',',1) laststate, MAX(e.date) laststatedate")
            ->setFrom("route r LEFT JOIN event e ON r.id = e.route")
            ->setWhere("r.end is NULL")
            ->setGroup("r.id")
            ->setOrder("r.begin DESC");
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $vehicle = (new db\Select())->setSelect("name")->setFrom("vehicle")->setWhere("id = " . $record['vehicle'])->run();
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'state' => $record['laststate'],
                    'date' => $record['laststatedate'],
                    'mileage' => $record['mileage'],
                    'vehicle' => empty($vehicle[0]['name']) ? '' : $vehicle[0]['name']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/event/route-table.html", [
            'caption' => 'Route List',
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
