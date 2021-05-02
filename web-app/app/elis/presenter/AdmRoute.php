<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Route administration presenter
 * @version 0.0.1 210112 created
 */
class AdmRoute extends Administration
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Route Administration');
    }

    public function newForm($model = null)
    {
        $vehicles = '';
        $vehiclesTmplt = new utils\Template("adm/route/form-option.html");
        foreach ((new db\Select())->setSelect("*")->setFrom("vehicle")->setOrder("name")->run() as $vehicle) {
            $vehiclesTmplt->setAllData([
                'value' => $vehicle['id'],
                'name' => $vehicle['name'],
                'selected' => $model instanceof model\Route && $model->getVehicle() == $vehicle['id'] ? 'selected' : ''
            ]);
            $vehicles .= (string)$vehiclesTmplt;
        }
        $this->adminTmplt->addData('content', new utils\Template("adm/route/form.html", [
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
                $messageTmplt->setData('message', 'Route ' . $model . ' has not been created.');
                $messageTmplt->setData('type', 'err');
            }
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setData('message', 'Code ' . $model->getCode() . ' already exists. New route ' . $model . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->newForm($model);
        }
    }

    protected function editForm($model = null)
    {
        $model = new model\Route($this->getParam(2));
        if ($model->getId()) {
            $vehicles = '';
            $vehiclesTmplt = new utils\Template("adm/route/form-option.html");
            foreach ((new db\Select())->setSelect("*")->setFrom("vehicle")->setOrder("name")->run() as $vehicle) {
                $vehiclesTmplt->setAllData([
                    'value' => $vehicle['id'],
                    'name' => $vehicle['name'],
                    'selected' => $model->getVehicle() == $vehicle['id'] ? 'selected' : ''
                ]);
                $vehicles .= (string)$vehiclesTmplt;
            }
            $this->adminTmplt->addData('content', new utils\Template("adm/route/form.html", [
                'caption' => 'Edit Route',
                'operation' => 'edit/' . $model->getId(),
                'name' => $model->getName(),
                'begin' => substr($model->getBegin(), 0, 10),
                'vehicles' => $vehicles,
                'description' => $model->getDescription()
            ]));
        } else {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
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
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setAllData([
                'message' => 'Name ' . $model->getCode() . ' already exists. route ' . $model . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->editForm($model);
        }
    }

    protected function deleteQuestion()
    {
        $model = new model\Route($this->getParam(2));
        if ($model->getId()) {
            $deleteQuestionTmplt = new utils\Template("adm/route/delete-yes-no.html", [
                'id' => $model->getId(),
                'route' => (string)$model
            ]);
            $this->adminTmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
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
                    'message' => "Route $model has not been deleted.",
                    'type' => 'err'
                ]);
            }
        } else {
            $messageTmplt->setAllData([
                'message' => "Route to delete does not exist.",
                'type' => 'err'
            ]);
        }
        $this->adminTmplt->setData('content', $messageTmplt);
        $this->table();
    }

    protected function table()
    {
        $tableRowTmplt = new utils\Template("adm/route/table-row.html");
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
        $this->adminTmplt->addData('content', new utils\Template("adm/route/table.html", [
            'caption' => 'Route List',
            'rows' => $rows
        ]));
        if (empty($rows)) {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'std',
                'message' => 'There is no record in the database'
            ]));
        }
    }

    protected function userForm()
    {
    }
}
