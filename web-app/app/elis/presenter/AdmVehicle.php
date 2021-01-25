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
        $this->tmplt->setData('title', 'Vehicle administration');
    }

    public function newForm($model = null)
    {
        $this->tmplt->addData('content', new utils\Template("adm/vehicle/form.html", [
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
        $messageTmplt = new utils\Template("adm/message.html");
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
            $this->tmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setData('message', 'UID ' . $model->getUid() . ' already exists. New vehicle ' . $model . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->tmplt->addData('content', $messageTmplt);
            $this->newForm($model);
        }
    }

    protected function editForm($model = null)
    {
        $model = new model\Vehicle($this->getParam(1));
        if ($model->getId()) {
            $this->tmplt->addData('content', new utils\Template("adm/vehicle/form.html", [
                'caption' => 'Edit Vehicle ' . $model,
                'operation' => 'edit/' . $model->getId(),
                'name' => (string)$model->getName(),
                'uid' => (string)$model->getUid(),
                'mileage' => (string)$model->getMileage(),
                'avg_consuption' => (float)$model->getAvgConsuption()
            ]));
        } else {
            $this->tmplt->addData('content', new utils\Template("adm/message.html", [
                'type' => 'err',
                'message' => 'Vehicle to edit does not exist.'
            ]));
            $this->table();
        }
    }

    protected function edit()
    {
        $model = new model\Vehicle($this->getParam(1));
        $model->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $model->setUid(filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_STRING));
        $model->setMileage(filter_input(INPUT_POST, 'mileage', FILTER_SANITIZE_NUMBER_INT));
        $model->setAvgConsuption(filter_input(INPUT_POST, 'avg_consuption', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $messageTmplt = new utils\Template("adm/message.html");
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
            $this->tmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setAllData([
                'message' => 'uid ' . $model->getUid() . ' already exists. Vehicle ' . $model . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->tmplt->addData('content', $messageTmplt);
            $this->editForm($model);
        }
    }

    protected function deleteQuestion()
    {
        $model = new model\Vehicle($this->getParam(1));
        if ($model->getId()) {
            $deleteQuestionTmplt = new utils\Template("adm/vehicle/delete-yes-no.html", [
                'id' => $model->getId(),
                'vehicle' => (string)$model
            ]);
            $this->tmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->tmplt->addData('content', new utils\Template("adm/message.html", [
                'type' => 'err',
                'message' => 'Vehicle to delete does not exist.'
            ]));
            $this->table();
        }
    }

    protected function delete()
    {
        $model = new model\Vehicle($this->getParam(1));
        $messageTmplt = new utils\Template("adm/message.html");
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
        $this->tmplt->setData('content', $messageTmplt);
        $this->table();
    }

    protected function table()
    {
        $tableRowTmplt = new utils\Template("adm/vehicle/table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("*")
            ->setFrom("vehicle")
            ->setOrder('name');
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'uid' => $record['uid'],
                    'mileage' => $record['mileage'],
                    'avg_consuption' => $record['avg_consuption']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->tmplt->addData('content', new utils\Template("adm/vehicle/table.html", [
            'caption' => 'Vehicle List',
            'rows' => $rows
        ]));
        if (empty($rows)) {
            $this->tmplt->addData('content', new utils\Template("adm/message.html", [
                'type' => 'std',
                'message' => 'There is no record in the database'
            ]));
        }
    }
}
