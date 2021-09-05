<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Event dispatcher administration presenter
 * @version 0.2.0 210620 packages in log, event selection
 * @version 0.1.4 210614 last error mess, getLog, getRoutes
 * @version 0.0.1 210610 created
 */
class DspEvent extends Dispatcher
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Dispatcher :: Event Administration');
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
        $vehicle = new model\Vehicle($route->getVehicle());
        $oldmileage = $vehicle->getMileage();
        $mileage = filter_input(INPUT_POST, 'mileage', FILTER_VALIDATE_INT) ? filter_input(INPUT_POST, 'mileage', FILTER_SANITIZE_NUMBER_INT) : $oldmileage;
        $formTmplt =  new utils\Template("dsp/event/lod-form.html", [
            'caption' => ' Route ' . $route . ' - Loading packages ',
            'mileage' => $mileage,
            'oldmileage' => $oldmileage,
            'route' => $route->getid()
        ]);
        $options = '';
        $result = model\Package::getPackagesToLoad();
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
                'label' => $item['name'],
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
                'message' => 'No packages to load.'
            ]));
            $this->log($route);
            return;
        }
        $ev = new model\Event("LOD");
        $ev->setRecorded($this->user->getId());
        $ev->setRoute($route->getId());
        $vehicle = new model\Vehicle($route->getVehicle());
        $oldmileage = $vehicle->getMileage();
        $mileage = filter_input(INPUT_POST, 'mileage', FILTER_VALIDATE_INT) ? filter_input(INPUT_POST, 'mileage', FILTER_SANITIZE_NUMBER_INT) : $oldmileage;
        $ev->setMileage($mileage);
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
            if ($mileage > $oldmileage) {
                $vehicle->setMileage($mileage)->save();
                $route->setMileage($route->getMileage() + ($mileage - $oldmileage))->save();
            }
        } else {
            $messageTmplt->setData('message', 'Event ' . $ev . ' has not been created.');
            $messageTmplt->setData('type', 'err');
        }
        $this->dspTmplt->addData('content', $messageTmplt);
        $this->log($route);
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
        $vehicle = new model\Vehicle($route->getVehicle());
        $oldmileage = $vehicle->getMileage();
        $mileage = filter_input(INPUT_POST, 'mileage', FILTER_VALIDATE_INT) ? filter_input(INPUT_POST, 'mileage', FILTER_SANITIZE_NUMBER_INT) : $oldmileage;
        $formTmplt =  new utils\Template("dsp/event/unl-form.html", [
            'caption' => ' Route ' . $route . ' - Unloading packages ',
            'mileage' => $mileage,
            'oldmileage' => $oldmileage,
            'route' => $route->getid()
        ]);
        $options = '';
        $result = $route->getLoadedPackages();
        if (empty($result)) {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'war',
                'message' => 'No packages to unload.'
            ]));
            $this->log($route);
            return;
        }
        $checkboxTmplt = new utils\Template("other/checkbox.html");
        foreach ($result as $item) {
            $checkboxTmplt->clearData()->setAllData([
                'name' => 'pck',
                'id' => $item['id'],
                'label' => $item['name'],
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
            $this->log($route);
            return;
        }
        $ev = new model\Event("UNL");
        $ev->setRecorded($this->user->getId());
        $ev->setRoute($route->getId());
        $vehicle = new model\Vehicle($route->getVehicle());
        $oldmileage = $vehicle->getMileage();
        $mileage = filter_input(INPUT_POST, 'mileage', FILTER_VALIDATE_INT) ? filter_input(INPUT_POST, 'mileage', FILTER_SANITIZE_NUMBER_INT) : $oldmileage;
        $ev->setMileage($mileage);
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
            if ($mileage > $oldmileage) {
                $vehicle->setMileage($mileage)->save();
                $route->setMileage($route->getMileage() + ($mileage - $oldmileage))->save();
            }
        } else {
            $messageTmplt->setData('message', 'Event ' . $ev . ' has not been created.' . db\MySQL::getLastError());
            $messageTmplt->setData('type', 'err');
        }
        $this->dspTmplt->addData('content', $messageTmplt);
        $this->log($route);
    }

    public function event()
    {
        $messageTmplt = new utils\Template("other/message.html");
        $route = new model\Route($this->getParam(3));
        if (!$route->getId()) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'Route does not exist.'
            ]));
            $this->table();
            return;
        }
        $eventType = strtoupper($this->getParam(2));
        $eventTypes = new model\CodeList('event-types.json');
        if (!($eventTypes->getItem($eventType) instanceof model\CodeListItem)) {
            $this->dspTmplt->addData('content', $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'Event type does not exist.'
            ]));
            $this->log($route);
            return;
        }
        $vehicle = new model\Vehicle($route->getVehicle());
        $oldmileage = $vehicle->getMileage();
        $mileage = filter_input(INPUT_POST, 'mileage', FILTER_VALIDATE_INT) ? filter_input(INPUT_POST, 'mileage', FILTER_SANITIZE_NUMBER_INT) : $oldmileage;
        if (filter_input(INPUT_POST, 'route', FILTER_SANITIZE_NUMBER_INT)) {
            if (filter_input(INPUT_POST, 'route', FILTER_SANITIZE_NUMBER_INT) == $route->getId()) {
                $ev = new model\Event($eventType);
                $ev->setRecorded($this->user->getId());
                $ev->setRoute($route->getId());
                $place = filter_input(INPUT_POST, 'place', FILTER_SANITIZE_NUMBER_INT);
                if ($place) {
                    $ev->setPlace($place);
                }
                $ev->setPlaceManual(filter_input(INPUT_POST, 'place_manual', FILTER_SANITIZE_STRING));
                $ev->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
                $ev->setMileage($mileage);
                if ($ev->save()) {
                    $messageTmplt->setData('message', 'Event ' . $ev . ' has been created.');
                    $messageTmplt->setData('type', 'suc');
                    if ($mileage > $oldmileage) {
                        $vehicle->setMileage($mileage)->save();
                        $route->setMileage($route->getMileage() + ($mileage - $oldmileage))->save();
                    }
                } else {
                    $messageTmplt->setData('message', 'Event ' . $ev . ' has not been created.' . db\MySQL::getLastError());
                    $messageTmplt->setData('type', 'err');
                }
                $this->dspTmplt->addData('content', $messageTmplt);
                $this->log($route);
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
            'mileage' => $mileage,
            'oldmileage' => $oldmileage,
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

    public function log(model\Route $route = null)
    {
        $messageTmplt = new utils\Template("other/message.html");
        if ($route == null) {
            $route = new model\Route($this->getParam(2));
        }
        if (!$route->getId()) {
            $messageTmplt->setAllData([
                'message' => "Route does not exist.",
                'type' => 'err'
            ]);
            $this->dspTmplt->setData('content', $messageTmplt);
            $this->table();
        }
        $nav = '';
        if (!$route->getEnd()) {
            foreach ([
                'ins' => 'event/ins',
                'lod' => 'lod-form',
                'unl' => 'unl-form',
                'wtg' => 'event/wtg',
                'onw' => 'event/onw',
                'rst' => 'event/rst',
                'rfl' => 'event/rfl',
                'acd' => 'event/acd',
                'oth' => 'event/oth'
            ] as $cap => $href) {
                $linkTmplt = new utils\Template("other/link-btn.html");
                $nav .= $linkTmplt->setAllData([
                    'href' => 'dsp-event/' . $href . '/' . $route->getId(),
                    'caption' => $cap
                ]) . ' ';
            }
        }
        $tableRowTmplt = new utils\Template("dsp/event/event-table-row.html");
        $rows = '';
        foreach ($route->getLog() as $record) {
            $tableRowTmplt->clearData()->setAllData([
                'id' => $record['id'],
                'date' => $record['date'],
                'type' => $record['type'],
                'packages' => $record['packages'],
                'user' => $record['username'],
                'mileage' => $record['mileage'],
                'place' => $record['placename'],
                'description' => $record['description']
            ]);
            $rows .= $tableRowTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/event/event-table.html", [
            'caption' => "Route $route log",
            'begin' => $route->getBegin(),
            'end' => $route->getend() ? $route->getend() : '-',
            'status' => $route->getend() ? 'Closed' : 'Active',
            'nav' => $nav,
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
        foreach (model\Route::getRoutes($this->user, ['DSP']) as $record) {
            $tableRowTmplt->clearData()->setAllData([
                'id' => $record['id'],
                'name' => $record['name'],
                'status' => $record['end'] ? 'Closed' : 'Active',
                'state' => $record['laststate'],
                'date' => $record['laststatedate'],
                'mileage' => $record['mileage'],
                'vehicle' => $record['vehiclename'],
                'description' => $record['description'],
                'class' => $record['end'] ? 'closed' : 'active'
            ]);
            $rows .= $tableRowTmplt;
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
