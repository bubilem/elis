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
        $this->tmplt->setData('title', 'Place administration');
    }

    public function newForm($model = null)
    {
        $this->tmplt->addData('content', new utils\Template("adm/place/form.html", [
            'caption' => 'New Place',
            'operation' => 'new',
            'name' => '',
            'code' => '',
            'street' => '',
            'gps' => ''
        ]));
    }

    protected function new()
    {
    }

    protected function editForm($model = null)
    {
    }

    protected function edit()
    {
    }

    protected function deleteQuestion()
    {
    }

    protected function delete()
    {
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
                    'address' => $record['street'] . ' ' . $record['city_name'],
                    'gps' => $record['gps']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->tmplt->addData('content', new utils\Template("adm/place/table.html", [
            'caption' => 'Place List',
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
