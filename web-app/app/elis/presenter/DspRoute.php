<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Route dispatcher administration presenter
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

    public function newForm($model = null)
    {
        $vehicles = '';
        $vehiclesTmplt = new utils\Template("dsp/route/route-form-option.html");
        foreach ((new db\Select())->setSelect("*")->setFrom("vehicle")->setOrder("name")->run() as $vehicle) {
            $vehiclesTmplt->setAllData([
                'value' => $vehicle['id'],
                'name' => $vehicle['name'],
                'selected' => $model instanceof model\Route && $model->getVehicle() == $vehicle['id'] ? 'selected' : ''
            ]);
            $vehicles .= (string)$vehiclesTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/route/route-form.html", [
            'caption' => 'New Route',
            'operation' => 'new',
            'name' => $model instanceof model\Route ? $model->getName() : '',
            'begin' => $model instanceof model\Route ? $model->getBegin() : date("Y-m-d"),
            'vehicles' => $vehicles,
            'description' => $model instanceof model\Route ? $model->getDescription() : ''
        ]));
    }

    protected function new()
    {
        $model = new model\Route();
        $model->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $model->setBegin(filter_input(INPUT_POST, 'begin', FILTER_SANITIZE_STRING));
        $model->setVehicle(filter_input(INPUT_POST, 'vehicle', FILTER_SANITIZE_NUMBER_INT));
        $model->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        $messageTmplt = new utils\Template("other/message.html");
        $sameNameRoute = (new db\Select())
            ->setSelect('id')
            ->setFrom('route')
            ->setWhere("name = '" . $model->getName() . "'")
            ->run();
        if (empty($sameNameRoute)) {
            if ($model->save()) {
                $messageTmplt->setData('message', 'Route ' . $model . ' has been created.');
                $messageTmplt->setData('type', 'suc');
            } else {
                $messageTmplt->setData('message', 'Route ' . $model . ' has not been created.' . db\MySQL::getLastError());
                $messageTmplt->setData('type', 'err');
            }
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setData('message', 'Code ' . $model->getCode() . ' already exists. New route ' . $model . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->newForm($model);
        }
    }

    protected function editForm($model = null)
    {
        $model = new model\Route($this->getParam(2));
        if ($model->getId()) {
            $vehicles = '';
            $vehiclesTmplt = new utils\Template("dsp/route/route-form-option.html");
            foreach ((new db\Select())->setSelect("*")->setFrom("vehicle")->setOrder("name")->run() as $vehicle) {
                $vehiclesTmplt->setAllData([
                    'value' => $vehicle['id'],
                    'name' => $vehicle['name'],
                    'selected' => $model->getVehicle() == $vehicle['id'] ? 'selected' : ''
                ]);
                $vehicles .= (string)$vehiclesTmplt;
            }
            $this->dspTmplt->addData('content', new utils\Template("dsp/route/route-form.html", [
                'caption' => 'Edit Route',
                'operation' => 'edit/' . $model->getId(),
                'name' => $model->getName(),
                'begin' => substr($model->getBegin(), 0, 10),
                'vehicles' => $vehicles,
                'description' => $model->getDescription()
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
        $model = new model\Route($this->getParam(2));
        $model->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $model->setBegin(filter_input(INPUT_POST, 'begin', FILTER_SANITIZE_STRING));
        $model->setVehicle(filter_input(INPUT_POST, 'vehicle', FILTER_SANITIZE_NUMBER_INT));
        $model->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        $messageTmplt = new utils\Template("other/message.html");
        $sameName = (new db\Select())
            ->setSelect('id')
            ->setFrom('route')
            ->setWhere("id <> " . $model->getId() . " AND name = '" . $model->getName() . "'")
            ->run();
        if (empty($sameName)) {
            if ($model->save()) {
                $messageTmplt->setAllData([
                    'message' => 'route ' . $model . ' has been saved.',
                    'type' => 'suc'
                ]);
            } else {
                $messageTmplt->setAllData([
                    'message' => 'route ' . $model . ' has not been saved. ' . db\MySQL::getLastError(),
                    'type' => 'err'
                ]);
            }
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setAllData([
                'message' => 'Name ' . $model->getCode() . ' already exists. route ' . $model . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->editForm($model);
        }
    }

    protected function deleteQuestion()
    {
        $model = new model\Route($this->getParam(2));
        if ($model->getId()) {
            $deleteQuestionTmplt = new utils\Template("dsp/route/route-delete-yes-no.html", [
                'id' => $model->getId(),
                'route' => (string)$model
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
        $model = new model\Route($this->getParam(2));
        $messageTmplt = new utils\Template("other/message.html");
        if ($model->getId()) {
            $messageTmplt->setAllData([
                'message' => "Route $model has been deleted.",
                'type' => 'suc'
            ]);
            if (!$model->delete()) {
                $messageTmplt->setAllData([
                    'message' => "Route $model has not been deleted." . db\MySQL::getLastError(),
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

    protected function table()
    {
        $tableRowTmplt = new utils\Template("dsp/route/route-table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("*")
            ->setFrom("route")
            ->setOrder("begin DESC");
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $vehicle = (new db\Select())->setSelect("name")->setFrom("vehicle")->setWhere("id = " . $record['vehicle'])->run();
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'begin' => $record['begin'],
                    'end' => $record['end'],
                    'mileage' => $record['mileage'],
                    'vehicle' => empty($vehicle[0]['name']) ? '' : $vehicle[0]['name'],
                    'description' => $record['description']
                ]);
                $rows .= $tableRowTmplt;
            }
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
        $model = new model\RouteHasUser();
        $model->setRoute($route->getId());
        $model->setUser(filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING));
        $model->setRole(filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING));
        $model->setAssigned(utils\Date::dbNow());
        $messageTmplt = new utils\Template("other/message.html");
        $sameUserInRoute = (new db\Select())
            ->setSelect('*')
            ->setFrom('route_has_user')
            ->setWhere('route = ' . $model->getRoute() . ' && user = ' . $model->getUser())
            ->run();
        if (!$model->getRoute() || !$model->getUser()) {
            $messageTmplt->setData('message', 'Fail.');
            $messageTmplt->setData('type', 'war');
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->newUserForm($model);
        } else if (empty($sameUserInRoute)) {
            if ($model->save()) {
                $messageTmplt->setData('message', 'User ' . $model->getUser() . ' has been added to route.');
                $messageTmplt->setData('type', 'suc');
            } else {
                $messageTmplt->setData('message', 'User ' . $model->getUser() . ' has not been added to route.' . db\MySQL::getLastError());
                $messageTmplt->setData('type', 'err');
            }
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->userTable();
        } else {
            $messageTmplt->setData('message', 'User ' . $model->getUser() . ' is already in route.');
            $messageTmplt->setData('type', 'war');
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->newUserForm($model);
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
        $model = new model\RouteHasUser(['route' => $this->getParam(2), 'user' => $this->getParam(3)]);
        $messageTmplt = new utils\Template("other/message.html");
        if ($model->getUser() && $model->getRoute()) {
            $messageTmplt->setAllData([
                'message' => "User has been removed.",
                'type' => 'suc'
            ]);
            if (!$model->delete()) {
                $messageTmplt->setAllData([
                    'message' => "User has not been removed.",
                    'type' => 'err'
                ]);
            }
        } else {
            $messageTmplt->setAllData([
                'message' => "Record to delete does not exist.",
                'type' => 'err'
            ]);
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
