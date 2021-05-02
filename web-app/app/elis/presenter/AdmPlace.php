<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * Place administration presenter
 * @version 0.0.1 210112 created
 */
class AdmPlace extends Administration
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Place Administration');
    }

    public function newForm($model = null)
    {
        $countryCode = '';
        $countryCodeTmplt = new utils\Template("adm/place/form-option.html");
        foreach ((new model\CodeList("countries.json"))->getItems() as $item) {
            $countryCodeTmplt->setAllData([
                'value' => $item->getCode(),
                'name' => $item->getName(),
                'selected' => $model instanceof model\Place && $model->getCountryCode() == $item->getCode() ? 'selected' : ''
            ]);
            $countryCode .= (string)$countryCodeTmplt;
        }
        $this->adminTmplt->addData('content', new utils\Template("adm/place/form.html", [
            'caption' => 'New Place',
            'operation' => 'new',
            'name' => $model instanceof model\Place ? $model->getName() : '',
            'code' => $model instanceof model\Place ? $model->getCode() : '',
            'street' => $model instanceof model\Place ? $model->getStreet() : '',
            'city_name' => $model instanceof model\Place ? $model->getCityName() : '',
            'city_code' => $model instanceof model\Place ? $model->getCityCode() : '',
            'country_code' => $countryCode,
            'gps' => $model instanceof model\Place ? $model->getGps() : ''
        ]));
    }

    protected function new()
    {
        $model = new model\Place();
        $model->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $model->setCode(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING));
        $model->setStreet(filter_input(INPUT_POST, 'street', FILTER_SANITIZE_STRING));
        $model->setCityName(filter_input(INPUT_POST, 'city_name', FILTER_SANITIZE_STRING));
        $model->setCityCode(filter_input(INPUT_POST, 'city_code', FILTER_SANITIZE_STRING));
        $model->setCountryCode(filter_input(INPUT_POST, 'country_code', FILTER_SANITIZE_STRING));
        $model->setGps(filter_input(INPUT_POST, 'gps', FILTER_SANITIZE_STRING));
        $messageTmplt = new utils\Template("other/message.html");
        $sameCodePlace = (new db\Select())
            ->setSelect('id')
            ->setFrom('place')
            ->setWhere("code = '" . $model->getCode() . "'")
            ->run();
        if (empty($sameCodePlace)) {
            if ($model->save()) {
                $messageTmplt->setData('message', 'Place ' . $model . ' has been created.');
                $messageTmplt->setData('type', 'suc');
            } else {
                $messageTmplt->setData('message', 'Place ' . $model . ' has not been created.');
                $messageTmplt->setData('type', 'err');
            }
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setData('message', 'Code ' . $model->getCode() . ' already exists. New place ' . $model . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->newForm($model);
        }
    }

    protected function editForm($model = null)
    {
        $model = new model\Place($this->getParam(2));
        if ($model->getId()) {
            $countryCode = '';
            $countryCodeTmplt = new utils\Template("adm/place/form-option.html");
            foreach ((new model\CodeList("countries.json"))->getItems() as $item) {
                $countryCodeTmplt->setAllData([
                    'value' => $item->getCode(),
                    'name' => $item->getName(),
                    'selected' => $model->getCountryCode() == $item->getCode() ? 'selected' : ''
                ]);
                $countryCode .= (string)$countryCodeTmplt;
            }
            $this->adminTmplt->addData('content', new utils\Template("adm/place/form.html", [
                'caption' => 'Edit Place ' . $model,
                'operation' => 'edit/' . $model->getId(),
                'name' => (string)$model->getName(),
                'code' => (string)$model->getCode(),
                'street' => (string)$model->getStreet(),
                'city_name' => (string)$model->getCityName(),
                'city_code' => (string)$model->getCityCode(),
                'country_code' => $countryCode,
                'gps' => (string)$model->getGps()
            ]));
        } else {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Place to edit does not exist.'
            ]));
            $this->table();
        }
    }

    protected function edit()
    {
        $model = new model\Place($this->getParam(2));
        $model->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $model->setCode(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING));
        $model->setStreet(filter_input(INPUT_POST, 'street', FILTER_SANITIZE_STRING));
        $model->setCityName(filter_input(INPUT_POST, 'city_name', FILTER_SANITIZE_STRING));
        $model->setCityCode(filter_input(INPUT_POST, 'city_code', FILTER_SANITIZE_STRING));
        $model->setCountryCode(filter_input(INPUT_POST, 'country_code', FILTER_SANITIZE_STRING));
        $model->setGps(filter_input(INPUT_POST, 'gps', FILTER_SANITIZE_STRING));
        $messageTmplt = new utils\Template("other/message.html");
        $sameCodePlace = (new db\Select())
            ->setSelect('id')
            ->setFrom('place')
            ->setWhere("id <> " . $model->getId() . " AND code = '" . $model->getCode() . "'")
            ->run();
        if (empty($sameCodePlace)) {
            if ($model->save()) {
                $messageTmplt->setAllData([
                    'message' => 'Place ' . $model . ' has been saved.',
                    'type' => 'suc'
                ]);
            } else {
                $messageTmplt->setAllData([
                    'message' => 'Place ' . $model . ' has not been saved. ' . db\MySQL::getLastError(),
                    'type' => 'err'
                ]);
            }
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setAllData([
                'message' => 'Code ' . $model->getCode() . ' already exists. Place ' . $model . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->editForm($model);
        }
    }

    protected function deleteQuestion()
    {
        $model = new model\Place($this->getParam(2));
        if ($model->getId()) {
            $deleteQuestionTmplt = new utils\Template("adm/place/delete-yes-no.html", [
                'id' => $model->getId(),
                'place' => (string)$model
            ]);
            $this->adminTmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Place to delete does not exist.'
            ]));
            $this->table();
        }
    }

    protected function delete()
    {
        $model = new model\Place($this->getParam(2));
        $messageTmplt = new utils\Template("other/message.html");
        if ($model->getId()) {
            $messageTmplt->setAllData([
                'message' => "Place $model has been deleted.",
                'type' => 'suc'
            ]);
            if (!$model->delete()) {
                $messageTmplt->setAllData([
                    'message' => "Place $model has not been deleted.",
                    'type' => 'err'
                ]);
            }
        } else {
            $messageTmplt->setAllData([
                'message' => "Place to delete does not exist.",
                'type' => 'err'
            ]);
        }
        $this->adminTmplt->setData('content', $messageTmplt);
        $this->table();
    }

    protected function table()
    {
        $tableRowTmplt = new utils\Template("adm/place/table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("*")
            ->setFrom("place")
            ->setOrder('name');
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'code' => $record['code'],
                    'address' => implode(', ', [$record['street'], $record['city_name'], $record['country_code']]),
                    'gps' => $record['gps']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->adminTmplt->addData('content', new utils\Template("adm/place/table.html", [
            'caption' => 'Place List',
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
