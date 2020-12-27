<?php

namespace elis\presenter;

use elis\model;
use elis\utils;
use elis\utils\db;

/**
 * User administration presenter
 * @version 0.0.1 201223 created
 */
class AdmUser extends Administration
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->tmplt->setData('title', 'User administration');
    }

    public function run()
    {
        switch ($this->getParam(0)) {
            case 'delete-question':
                $this->deleteQuestion();
                break;
            case 'delete':
                $this->delete();
                break;
            default:
                $this->userTable();
        }
        echo $this->tmplt;
    }

    private function userTable()
    {
        $tableRowTmplt = new utils\Template("adm/user/table-row.html");
        $rows = '';
        $query = (new db\Select())->setSelect("*")->setFrom("user")->setOrder('surname,name');
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'surname' => $record['surname'],
                    'email' => $record['email']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->tmplt->addData('content', new utils\Template("adm/user/table.html", ['rows' => $rows]));
        if (empty($rows)) {
            $this->tmplt->addData('content', new utils\Template("adm/message.html", [
                'type' => 'std',
                'message' => 'There is no record in the database'
            ]));
        }
    }

    private function deleteQuestion()
    {
        $user = new model\User($this->getParam(1));
        if ($user->getId()) {
            $deleteQuestionTmplt = new utils\Template("adm/user/delete-yes-no.html", [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname()
            ]);
            $this->tmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->tmplt->addData('content', new utils\Template("adm/message.html", [
                'type' => 'err',
                'message' => 'User to delete does not exist.'
            ]));
            $this->userTable();
        }
    }

    private function delete()
    {
        $user = new model\User($this->getParam(1));
        $messageTmplt = new utils\Template("adm/message.html");
        $messageTmplt->setData('type', 'err');
        if ($user->getId()) {
            $message = 'The user ' . $user->getName() . ' ' . $user->getSurname();
            if ($user->delete()) {
                $messageTmplt->setData('message', $message . ' has been deleted.');
                $messageTmplt->setData('type', 'suc');
            } else {
                $messageTmplt->setData('message', $message . ' has not been deleted.');
            }
        } else {
            $messageTmplt->setData('message', 'User to delete does not exist.');
        }
        $this->tmplt->setData('content', $messageTmplt);
        $this->userTable();
    }
}
