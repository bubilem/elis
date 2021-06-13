<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Vehicle administration presenter
 * @version 0.0.1 210102 created
 */
class AdmVehicle extends Administration
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Admin :: Vehicle Administration');
    }

    public function newForm($model = null)
    {
        $this->adminTmplt->addData('content', new utils\Template("adm/vehicle/form.html", [
            'caption' => 'New Vehicle',
            'operation' => 'new',
            'name' => $model instanceof model\Vehicle ? $model->getName() : '',
            'uid' => $model instanceof model\Vehicle ? $model->getUid() : '',
            'mileage' => $model instanceof model\Vehicle ? $model->getMileage() : '',
            'avg_consuption' => $model instanceof model\Vehicle ? $model->getAvgConsuption() : ''
        ]));
    }

    protected function new()
    {
        $model = new model\Vehicle();
        $model->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $model->setUid(filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING));
        $model->setMileage(filter_input(INPUT_POST, 'mileage', FILTER_SANITIZE_NUMBER_INT));
        $model->setAvgConsuption(filter_input(INPUT_POST, 'avg_consuption', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $messageTmplt = new utils\Template("other/message.html");
        $sameUidVehicle = (new db\Select())
            ->setSelect('id')
            ->setFrom('vehicle')
            ->setWhere("uid = '" . $model->getUid() . "'")
            ->run();
        if (empty($sameUidVehicle)) {
            if ($model->save()) {
                $messageTmplt->setData('message', 'Vehicle ' . $model . ' has been created.');
                $messageTmplt->setData('type', 'suc');
            } else {
                $messageTmplt->setData('message', 'Vehicle ' . $model . ' has not been created.');
                $messageTmplt->setData('type', 'err');
            }
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setData('message', 'UID ' . $model->getUid() . ' already exists. New vehicle ' . $model . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->newForm($model);
        }
    }

    protected function editForm($model = null)
    {
        $model = new model\Vehicle($this->getParam(2));
        if ($model->getId()) {
            $this->adminTmplt->addData('content', new utils\Template("adm/vehicle/form.html", [
                'caption' => 'Edit Vehicle ' . $model,
                'operation' => 'edit/' . $model->getId(),
                'name' => (string)$model->getName(),
                'uid' => (string)$model->getUid(),
                'mileage' => (string)$model->getMileage(),
                'avg_consuption' => (float)$model->getAvgConsuption()
            ]));
        } else {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Vehicle to edit does not exist.'
            ]));
            $this->table();
        }
    }

    protected function edit()
    {
        $model = new model\Vehicle($this->getParam(2));
        $model->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $model->setUid(filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING));
        $model->setMileage(filter_input(INPUT_POST, 'mileage', FILTER_SANITIZE_NUMBER_INT));
        $model->setAvgConsuption(filter_input(INPUT_POST, 'avg_consuption', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $messageTmplt = new utils\Template("other/message.html");
        $sameUidVehicle = (new db\Select())
            ->setSelect('id')
            ->setFrom('vehicle')
            ->setWhere("id <> " . $model->getId() . " AND uid = '" . $model->getUid() . "'")
            ->run();
        if (empty($sameUidVehicle)) {
            if ($model->save()) {
                $messageTmplt->setAllData([
                    'message' => 'Vehicle ' . $model . ' has been saved.',
                    'type' => 'suc'
                ]);
            } else {
                $messageTmplt->setAllData([
                    'message' => 'Vehicle ' . $model . ' has not been saved. ' . db\MySQL::getLastError(),
                    'type' => 'err'
                ]);
            }
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setAllData([
                'message' => 'uid ' . $model->getUid() . ' already exists. Vehicle ' . $model . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->editForm($model);
        }
    }

    protected function deleteQuestion()
    {
        $model = new model\Vehicle($this->getParam(2));
        if ($model->getId()) {
            $deleteQuestionTmplt = new utils\Template("adm/vehicle/delete-yes-no.html", [
                'id' => $model->getId(),
                'vehicle' => (string)$model
            ]);
            $this->adminTmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Vehicle to delete does not exist.'
            ]));
            $this->table();
        }
    }

    protected function delete()
    {
        $model = new model\Vehicle($this->getParam(2));
        $messageTmplt = new utils\Template("other/message.html");
        if ($model->getId()) {
            $messageTmplt->setAllData([
                'message' => "Vehicle $model has been deleted.",
                'type' => 'suc'
            ]);
            if (!$model->delete()) {
                $messageTmplt->setAllData([
                    'message' => "Vehicle $model has not been deleted.",
                    'type' => 'err'
                ]);
            }
        } else {
            $messageTmplt->setAllData([
                'message' => "Vehicle to delete does not exist.",
                'type' => 'err'
            ]);
        }
        $this->adminTmplt->setData('content', $messageTmplt);
        $this->table();
    }

    protected function table()
    {
        $tableRowTmplt = new utils\Template("adm/vehicle/table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("v.*, GROUP_CONCAT(CONCAT(r.name,' ',DATE_FORMAT(r.begin, '%Y-%m-%d')) ORDER BY r.begin DESC) route")
            ->setFrom("vehicle v LEFT JOIN route r ON v.id = r.vehicle AND r.begin < NOW() AND r.end IS NULL")
            ->setGroup("v.id")
            ->setOrder('v.name');
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'uid' => $record['uid'],
                    'route' => $record['route'],
                    'mileage' => $record['mileage'],
                    'avg_consuption' => $record['avg_consuption']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->adminTmplt->addData('content', new utils\Template("adm/vehicle/table.html", [
            'caption' => 'Vehicle List',
            'rows' => $rows
        ]));
        if (empty($rows)) {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'std',
                'message' => 'There is no record in the database'
            ]));
        }
    }
}
