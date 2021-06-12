<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;
use elis\utils\Template;

/**
 * Package dispatcher administration presenter
 * @version 0.0.1 210610 created
 */
class DspPackage extends Dispatcher
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', 'Package Administration');
    }

    public function newForm($model = null)
    {
        if ($model == null) {
            if ($this->getParam(2) == 'package-type') {
                $this->newFormPackageType();
                return;
            }
            $packageType = (new model\CodeList("package-types.json"))->getItem($this->getParam(2));
            if ($packageType == null) {
                $messageTmplt = new utils\Template("other/message.html", [
                    'message' => 'Wrong packet type definition.',
                    'type' => 'err'
                ]);
                $this->dspTmplt->addData('content', $messageTmplt);
                $this->newFormPackageType();
                return;
            }
        } else if ($model instanceof model\Package) {
            $packageType = (new model\CodeList("package-types.json"))->getItem($model->getType());
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/package/form.html", [
            'caption' => 'New Package ' . $packageType->getCode(),
            'operation' => 'new',
            'code' => $model instanceof model\Package ? $model->getCode() : '',
            'type' => $model instanceof model\Package ? $model->getType() : $packageType->getCode(),
            'width' => $model instanceof model\Package ? $model->getWidth() : $packageType->getWidth(),
            'height' => $model instanceof model\Package ? $model->getHeight() : $packageType->getHeight(),
            'lenght' => $model instanceof model\Package ? $model->getLenght() : $packageType->getLenght(),
            'weight' => $model instanceof model\Package ? $model->getWeight() : $packageType->getMaxNetLoad(),
            'description' => $model instanceof model\Package ? $model->getDescription() : ''
        ]));
    }

    public function newFormPackageType($model = null)
    {
        $packageTypes = '';
        $packageTypeTmplt = new utils\Template("dsp/package/nav-package-type.html");
        foreach ((new model\CodeList("package-types.json"))->getItems() as $item) {
            $packageTypeTmplt->setAllData([
                'code' => $item->getCode(),
                'name' => $item->getName(),
                'standard' => $item->getStandard(),
                'lenght' => $item->getLenght(),
                'width' => $item->getWidth(),
                'height' => $item->getHeight(),
                'max_gross_weight' => $item->getMaxGrossWeight(),
                'max_net_load' => $item->getMaxNetLoad(),
                'volume' => $item->getVolume()
            ]);
            $packageTypes .= (string)$packageTypeTmplt;
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/package/nav-package-types.html", [
            'caption' => 'Select package type',
            'package-types' => $packageTypes
        ]));
    }

    protected function new()
    {
        $model = new model\Package();
        $model->setCode(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING));
        $model->setType(filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING));
        $model->setWidth(filter_input(INPUT_POST, 'width', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $model->setHeight(filter_input(INPUT_POST, 'height', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $model->setLenght(filter_input(INPUT_POST, 'lenght', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $model->setWeight(filter_input(INPUT_POST, 'weight', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $model->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        $model->setState('ACP');
        $messageTmplt = new utils\Template("other/message.html");
        $sameCodePackage = (new db\Select())
            ->setSelect('id')
            ->setFrom('package')
            ->setWhere("code = '" . $model->getCode() . "'")
            ->run();
        if (empty($sameCodePackage)) {
            if ($model->save()) {
                $messageTmplt->setData('message', 'Package ' . $model . ' has been created.');
                $messageTmplt->setData('type', 'suc');
                $model->createLog("ACP")->save();
            } else {
                $messageTmplt->setData('message', 'Package ' . $model . ' has not been created.');
                $messageTmplt->setData('type', 'err');
            }
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setData('message', 'Code ' . $model->getCode() . ' already exists. New package ' . $model . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->newForm($model);
        }
    }

    protected function editForm($model = null)
    {
        $model = new model\Package($this->getParam(2));
        if ($model->getId()) {
            $this->dspTmplt->addData('content', new utils\Template("dsp/package/form.html", [
                'caption' => 'New Package ' . $model->getCode(),
                'operation' => 'edit/' . $model->getId(),
                'code' => $model->getCode(),
                'type' => $model->getType(),
                'width' => $model->getWidth(),
                'height' => $model->getHeight(),
                'lenght' => $model->getLenght(),
                'weight' => $model->getWeight(),
                'description' => $model->getDescription()
            ]));
        } else {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Package to edit does not exist.'
            ]));
            $this->table();
        }
    }

    protected function edit()
    {
        $model = new model\Package($this->getParam(2));
        $model->setCode(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING));
        $model->setType(filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING));
        $model->setWidth(filter_input(INPUT_POST, 'width', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $model->setHeight(filter_input(INPUT_POST, 'height', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $model->setLenght(filter_input(INPUT_POST, 'lenght', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $model->setWeight(filter_input(INPUT_POST, 'weight', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $model->setDescription(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
        $messageTmplt = new utils\Template("other/message.html");
        $sameCodePackage = (new db\Select())
            ->setSelect('id')
            ->setFrom('package')
            ->setWhere("id <> " . $model->getId() . " AND code = '" . $model->getCode() . "'")
            ->run();
        if (empty($sameCodePackage)) {
            if ($model->save()) {
                $messageTmplt->setAllData([
                    'message' => 'Package ' . $model . ' has been saved.',
                    'type' => 'suc'
                ]);
            } else {
                $messageTmplt->setAllData([
                    'message' => 'Package ' . $model . ' has not been saved. ' . db\MySQL::getLastError(),
                    'type' => 'err'
                ]);
            }
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setAllData([
                'message' => 'Code ' . $model->getCode() . ' already exists. Package ' . $model . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->dspTmplt->addData('content', $messageTmplt);
            $this->editForm($model);
        }
    }

    protected function deleteQuestion()
    {
        $model = new model\Package($this->getParam(2));
        if ($model->getId()) {
            $deleteQuestionTmplt = new utils\Template("dsp/package/delete-yes-no.html", [
                'id' => $model->getId(),
                'package' => (string)$model
            ]);
            $this->dspTmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'Package to delete does not exist.'
            ]));
            $this->table();
        }
    }

    protected function delete()
    {
        $model = new model\Package($this->getParam(2));
        $messageTmplt = new utils\Template("other/message.html");
        if ($model->getId()) {
            $messageTmplt->setAllData([
                'message' => "Package $model has been deleted.",
                'type' => 'suc'
            ]);
            if (!$model->delete()) {
                $messageTmplt->setAllData([
                    'message' => "Package $model has not been deleted.",
                    'type' => 'err'
                ]);
            }
        } else {
            $messageTmplt->setAllData([
                'message' => "Package to delete does not exist.",
                'type' => 'err'
            ]);
        }
        $this->dspTmplt->setData('content', $messageTmplt);
        $this->table();
    }

    protected function toWtg()
    {
        $model = new model\Package($this->getParam(2));
        $messageTmplt = new utils\Template("other/message.html");
        if ($model->getId()) {
            $model->setState('WTG');
            if ($model->save()) {
                $model->createLog("WTG")->save();
                $messageTmplt->setAllData([
                    'message' => "State of package $model has been setted to WTG.",
                    'type' => 'suc'
                ]);
            } else {
                $messageTmplt->setAllData([
                    'message' => "State of package $model has not been setted to WTG.",
                    'type' => 'err'
                ]);
            }
        } else {
            $messageTmplt->setAllData([
                'message' => "Package to change does not exist.",
                'type' => 'err'
            ]);
        }
        $this->dspTmplt->setData('content', $messageTmplt);
        $this->table();
    }

    public function log()
    {
        $model = new model\Package($this->getParam(2));
        if (!$model->getId()) {
            $messageTmplt = new utils\Template("other/message.html");
            $messageTmplt->setAllData([
                'message' => "Package does not exist.",
                'type' => 'err'
            ]);
            $this->dspTmplt->setData('content', $messageTmplt);
            $this->table();
        }
        $tableRowTmplt = new utils\Template("dsp/package/log-table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("l.*, e.type eventtype")
            ->setFrom("package_log l LEFT JOIN event e ON l.event = e.id")
            ->setWhere("l.package = " . $model->getId())
            ->setOrder('l.date DESC');
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'date' => $record['date'],
                    'package' => $record['package'],
                    'state' => $record['state'],
                    'event' => $record['eventtype']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/package/log-table.html", [
            'caption' => "Package $model log",
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
        $tableRowTmplt = new utils\Template("dsp/package/table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("p.*, SUBSTRING_INDEX(GROUP_CONCAT(l.state ORDER BY l.date DESC),',',1) laststate")
            ->setFrom("package p JOIN package_log l ON p.id = l.package")
            ->setGroup('p.id')
            ->setOrder('p.id DESC');
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            $btnLink = new Template("other/link-btn.html", ['caption' => 'WTG']);
            foreach ($queryResult as $record) {
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'code' => $record['code'],
                    'type' => $record['type'],
                    'state' => $record['laststate'],
                    'dimension' => implode(' x ', [$record['width'], $record['height'], $record['lenght']]),
                    'weight' => $record['weight'],
                    'description' => $record['description']
                ]);
                if ($record['laststate'] == 'ACP') {
                    $btnLink->setData('href', 'dsp-package/to-wtg/' . $record['id']);
                    $tableRowTmplt->setData('wtg', $btnLink);
                } else {
                    $tableRowTmplt->setData('wtg', '');
                }
                $rows .= $tableRowTmplt;
            }
        }
        $this->dspTmplt->addData('content', new utils\Template("dsp/package/table.html", [
            'caption' => 'Package List',
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
