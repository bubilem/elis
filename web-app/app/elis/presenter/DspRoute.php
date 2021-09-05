<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Route dispatcher administration presenter
 * @version 0.2.0 210620 close the route, table
 * @version 0.1.4 210614 last error mess
 * @version 0.0.1 210112 created
 */
class DspRoute extends Dispatcher
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Dispatcher :: Route Administration');
    }

    public function newForm($route = null)
    {
        $vehicles = '';
        $vehiclesTmplt = new utils\Template("dsp/route/route-form-option.html");
        foreach ((new db\Select())->setSelect("*")->setFrom("vehicle")->setOrder("name")->run() as $vehicle) {
            $vehiclesTmplt->setAllData([
                'value' => $vehicle['id'],
                'name' => $vehicle['name'],
                'selected' => $route instanceof model\Route && $route->getVehicle() == $vehicle['id'] ? 'selected' : ''
            ]);
            $vehicles .= (string)$vehiclesTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/route/route-form.html", [
            'caption' => 'New Route',
            'operation' => 'new',
            'name' => $route instanceof model\Route ? $route->getName() : '',
            'begin' => $route instanceof model\Route ? $route->getBegin() : date("Y-m-d"),
            'vehicles' => $vehicles,
            'description' => $route instanceof model\Route ? $route->getDescription() : ''
        ]));
    }

    protected function new()
    {
        $route = new model\Route();
        $route->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $route->setVehicle(filter_input(INPUT_POST, 'vehicle', FILTER_SANITIZE_NUMBER_INT));
        $route->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        $route->setMileage(0);
        $messageTmplt = new utils\Template("other/message.html");
        $sameNameRoute = (new db\Select())
            ->setSelect('id')
            ->setFrom('route')
            ->setWhere("name = '" . $route->getName() . "'")
            ->run();
        if (empty($sameNameRoute)) {
            if ($route->save()) {
                $rhu = new model\RouteHasUser();
                $rhu->setRoute($route->getId());
                $rhu->setUser($this->user->getId());
                $rhu->setRole('DSP');
                $rhu->setAssigned(utils\Date::dbNow());
                if ($rhu->save()) {
                    $messageTmplt->setData('message', 'Route ' . $route . ' has been created. User ' . $rhu->getUser() . ' has been added to route.');
                    $messageTmplt->setData('type', 'suc');
                } else {
                    $messageTmplt->setData('message', 'Route ' . $route . ' has been created. User ' . $rhu->getUser() . ' has not been added to route.' . db\MySQL::getLastError());
                    $messageTmplt->setData('type', 'err');
                }
            } else {
                $messageTmplt->setData('message', 'Route ' . $route . ' has not been created.' . db\MySQL::getLastError());
                $messageTmplt->setData('type', 'err');
            }
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setData('message', 'Code ' . $route->getCode() . ' already exists. New route ' . $route . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->newForm($route);
        }
    }

    protected function editForm($route = null)
    {
        $route = new model\Route($this->getParam(2));
        if ($route->getId()) {
            $vehicles = '';
            $vehiclesTmplt = new utils\Template("dsp/route/route-form-option.html");
            foreach ((new db\Select())->setSelect("*")->setFrom("vehicle")->setOrder("name")->run() as $vehicle) {
                $vehiclesTmplt->setAllData([
                    'value' => $vehicle['id'],
                    'name' => $vehicle['name'],
                    'selected' => $route->getVehicle() == $vehicle['id'] ? 'selected' : ''
                ]);
                $vehicles .= (string)$vehiclesTmplt;
            }
            $this->dspTmplt->addData('content', new utils\Template("dsp/route/route-form.html", [
                'caption' => 'Edit Route',
                'operation' => 'edit/' . $route->getId(),
                'name' => $route->getName(),
                'begin' => substr($route->getBegin(), 0, 10),
                'vehicles' => $vehicles,
                'description' => $route->getDescription()
            ]));
        } else {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Route to edit does not exist.'
            ]));
            $this->table();
        }
    }

    protected function edit()
    {
        $route = new model\Route($this->getParam(2));
        $route->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $route->setBegin(filter_input(INPUT_POST, 'begin', FILTER_SANITIZE_STRING));
        $route->setVehicle(filter_input(INPUT_POST, 'vehicle', FILTER_SANITIZE_NUMBER_INT));
        $route->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        $messageTmplt = new utils\Template("other/message.html");
        $sameName = (new db\Select())
            ->setSelect('id')
            ->setFrom('route')
            ->setWhere("id <> " . $route->getId() . " AND name = '" . $route->getName() . "'")
            ->run();
        if (empty($sameName)) {
            if ($route->save()) {
                $messageTmplt->setAllData([
                    'message' => 'route ' . $route . ' has been saved.',
                    'type' => 'suc'
                ]);
            } else {
                $messageTmplt->setAllData([
                    'message' => 'route ' . $route . ' has not been saved. ' . db\MySQL::getLastError(),
                    'type' => 'err'
                ]);
            }
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setAllData([
                'message' => 'Name ' . $route->getCode() . ' already exists. route ' . $route . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->editForm($route);
        }
    }

    protected function deleteQuestion()
    {
        $route = new model\Route($this->getParam(2));
        if ($route->getId()) {
            $deleteQuestionTmplt = new utils\Template("dsp/route/route-delete-yes-no.html", [
                'id' => $route->getId(),
                'route' => (string)$route
            ]);
            $this->dspTmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Route to delete does not exist.'
            ]));
            $this->table();
        }
    }

    protected function delete()
    {
        $route = new model\Route($this->getParam(2));
        $messageTmplt = new utils\Template("other/message.html");
        if ($route->getId()) {
            $messageTmplt->setAllData([
                'message' => "Route $route has been deleted.",
                'type' => 'suc'
            ]);
            if (!$route->delete()) {
                $messageTmplt->setAllData([
                    'message' => "Route $route has not been deleted." . db\MySQL::getLastError(),
                    'type' => 'err'
                ]);
            }
        } else {
            $messageTmplt->setAllData([
                'message' => "Route to delete does not exist.",
                'type' => 'err'
            ]);
        }
        $this->dspTmplt->setData('content', $messageTmplt);
        $this->table();
    }

    protected function close()
    {
        $route = new model\Route($this->getParam(2));
        $messageTmplt = new utils\Template("other/message.html");
        if ($route->getId()) {
            $loadedPackages = $route->getLoadedPackages();
            if (!empty($loadedPackages)) {
                $messageTmplt->setAllData([
                    'type' => 'war',
                    'message' => 'There are loaded packages in the route.'
                ]);
            } else {
                if ($this->getParam(3) == 'go') {
                    $route->setEnd(utils\Date::dbNow());
                    if ($route->save()) {
                        $messageTmplt->setAllData([
                            'message' => 'route ' . $route . ' has been closed.',
                            'type' => 'suc'
                        ]);
                    } else {
                        $messageTmplt->setAllData([
                            'message' => 'route ' . $route . ' has not been closed. ' . db\MySQL::getLastError(),
                            'type' => 'err'
                        ]);
                    }
                } else {
                    $deleteQuestionTmplt = new utils\Template("dsp/route/route-close-yes-no.html", [
                        'id' => $route->getId(),
                        'route' => (string)$route
                    ]);
                    $this->dspTmplt->addData('content', $deleteQuestionTmplt);
                    return;
                }
            }
        } else {
            $messageTmplt->setAllData([
                'type' => 'err',
                'message' => 'Route to close does not exist.'
            ]);
        }
        $this->dspTmplt->setData('content', $messageTmplt);
        $this->table();
    }

    protected function table()
    {
        $tableRowTmplt = new utils\Template("dsp/route/route-table-row.html");
        $btnLinkTmplt = new utils\Template("other/link-btn.html");
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
                'class' => $record['end'] ? 'closed' : 'active',
                'edt' => '',
                'usr' => '',
                'close' => ''
            ]);
            if (empty($record['end'])) {
                $btnLinkTmplt->setAllData(['caption' => 'CLOSE', 'href' => 'dsp-route/close/' . $record['id']]);
                $tableRowTmplt->setData('close', (string)$btnLinkTmplt);
                $btnLinkTmplt->setAllData(['caption' => '&#9998; edt', 'href' => 'dsp-route/edit-form/' . $record['id']]);
                $tableRowTmplt->setData('edt', (string)$btnLinkTmplt);
                $btnLinkTmplt->setAllData(['caption' => '&#9786; usr', 'href' => 'dsp-route/user-table/' . $record['id']]);
                $tableRowTmplt->setData('usr', (string)$btnLinkTmplt);
            }
            $rows .= $tableRowTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/route/route-table.html", [
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

    public function newUserForm()
    {
        $route = new model\Route($this->getParam(2));
        $users = '';
        $usersTmplt = new utils\Template("dsp/route/user-form-option.html");
        foreach ((new db\Select())
            ->setSelect("*")
            ->setFrom("user")
            ->setWhere("id NOT IN(SELECT user FROM route_has_user WHERE route = " . $route->getId() . ")")
            ->setOrder("surname")
            ->run() as $user) {
            $usersTmplt->setAllData([
                'value' => $user['id'],
                'name' => $user['surname'] . " " . $user['name']
            ]);
            $users .= (string)$usersTmplt;
        }
        if (empty($users)) {
            $messageTmplt = new utils\Template("other/message.html");
            $messageTmplt->setData('message', 'No free user.');
            $messageTmplt->setData('type', 'war');
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->userTable();
        } else {
            $roles = '';
            $rolesTmplt = new utils\Template("dsp/route/user-form-option.html");
            foreach ((new model\CodeList("user-roles.json"))->getItems() as $role) {
                if (in_array($role->getCode(), ['ADM', 'MNG'])) {
                    continue;
                }
                $rolesTmplt->setAllData([
                    'value' => $role->getCode(),
                    'name' => $role->getName()
                ]);
                $roles .= (string)$rolesTmplt;
            }
            $this->dspTmplt->addData('content', new utils\Template("dsp/route/user-form.html", [
                'caption' => 'Route ' . $route->getName() . ' new user',
                'operation' => 'new-user/' . $route->getId(),
                'users' => $users,
                'roles' => $roles
            ]));
        }
    }

    protected function newUser()
    {
        $route = new model\Route($this->getParam(2));
        $rhu = new model\RouteHasUser();
        $rhu->setRoute($route->getId());
        $rhu->setUser(filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING));
        $rhu->setRole(filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING));
        $rhu->setAssigned(utils\Date::dbNow());
        $messageTmplt = new utils\Template("other/message.html");
        $sameUserInRoute = (new db\Select())
            ->setSelect('*')
            ->setFrom('route_has_user')
            ->setWhere('route = ' . $rhu->getRoute() . ' && user = ' . $rhu->getUser())
            ->run();
        if (!$rhu->getRoute() || !$rhu->getUser()) {
            $messageTmplt->setData('message', 'Fail.');
            $messageTmplt->setData('type', 'war');
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->newUserForm($rhu);
        } else if (empty($sameUserInRoute)) {
            if ($rhu->save()) {
                $messageTmplt->setData('message', 'User ' . $rhu->getUser() . ' has been added to route.');
                $messageTmplt->setData('type', 'suc');
            } else {
                $messageTmplt->setData('message', 'User ' . $rhu->getUser() . ' has not been added to route.' . db\MySQL::getLastError());
                $messageTmplt->setData('type', 'err');
            }
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->userTable();
        } else {
            $messageTmplt->setData('message', 'User ' . $rhu->getUser() . ' is already in route.');
            $messageTmplt->setData('type', 'war');
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->newUserForm($rhu);
        }
    }

    protected function userDeleteQuestion()
    {
        $route = new model\Route($this->getParam(2));
        $user = new model\User($this->getParam(3));
        if ($route->getId() && $user->getId()) {
            $deleteQuestionTmplt = new utils\Template("dsp/route/user-delete-yes-no.html", [
                'route' => $route->getId(),
                'user' => $user->getId(),
                'name' => (string)$user
            ]);
            $this->dspTmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Route to delete does not exist.'
            ]));
            $this->table();
        }
    }

    protected function userDelete()
    {
        $rhu = new model\RouteHasUser(['route' => $this->getParam(2), 'user' => $this->getParam(3)]);
        $messageTmplt = new utils\Template("other/message.html");
        if (!$rhu->getUser() || !$rhu->getRoute()) {
            $messageTmplt->setAllData([
                'message' => "Record to delete does not exist.",
                'type' => 'err'
            ]);
        } else if ($this->user->getId() == $rhu->getUser()) {
            $messageTmplt->setAllData([
                'message' => "You can't remove yourself.",
                'type' => 'war'
            ]);
        } else {
            if ($rhu->delete()) {
                $messageTmplt->setAllData([
                    'message' => "User has been removed.",
                    'type' => 'suc'
                ]);
            } else {
                $messageTmplt->setAllData([
                    'message' => "User has not been removed.",
                    'type' => 'err'
                ]);
            }
        }
        $this->dspTmplt->setData('content', $messageTmplt);
        $this->userTable();
    }

    protected function userTable()
    {
        $route = new model\Route($this->getParam(2));
        $tableRowTmplt = new utils\Template("dsp/route/user-table-row.html");
        $rows = '';
        foreach ($route->getUsersInRoute() as $record) {
            $tableRowTmplt->clearData()->setAllData([
                'name' => $record['name'],
                'surname' => $record['surname'],
                'email' => $record['email'],
                'role' => $record['role'],
                'assigned' => $record['assigned'],
                'route' => $route->getId(),
                'user' => $record['id']
            ]);
            $rows .= $tableRowTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/route/user-table.html", [
            'caption' => 'Route ' . $route->getName() . ' has users',
            'route' => $route->getId(),
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
