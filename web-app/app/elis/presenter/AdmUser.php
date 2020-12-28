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
            case 'new-form':
                $this->newForm();
                break;
            case 'new':
                $this->new();
                break;
            case 'edit-form':
                $this->editForm();
                break;
            case 'edit':
                $this->edit();
                break;
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

    private function newForm(model\User $user = null)
    {
        $this->tmplt->addData('content', new utils\Template("adm/user/form.html", [
            'caption' => 'New User',
            'operation' => 'new',
            'name' => $user instanceof model\User ? $user->getName() : '',
            'surname' => $user instanceof model\User ? $user->getSurname() : '',
            'email' => $user instanceof model\User ? $user->getEmail() : '',
            'password-example' => utils\Secure::randPassword(),
        ]));
    }

    private function new()
    {
        $user = new model\User();
        $user->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $user->setSurname(filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING));
        $user->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        if (filter_input(INPUT_POST, 'password')) {
            $user->setPassword(utils\Secure::hash(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING)));
        }
        $messageTmplt = new utils\Template("adm/message.html");
        $sameEmailUsers = (new db\Select())
            ->setSelect('id')
            ->setFrom('user')
            ->setWhere("email = '" . $user->getEmail() . "'")
            ->run();
        if (empty($sameEmailUsers)) {
            if ($user->save()) {
                $messageTmplt->setData('message', 'User ' . $user . ' has been created.');
                $messageTmplt->setData('type', 'suc');
            } else {
                $messageTmplt->setData('message', 'User ' . $user . ' has not been created.');
                $messageTmplt->setData('type', 'err');
            }
            $this->tmplt->addData('content', $messageTmplt);
            $this->userTable();
        } else {
            $messageTmplt->setData('message', 'Email ' . $user->getEmail() . ' already exists. New user ' . $user . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->tmplt->addData('content', $messageTmplt);
            $this->newForm($user);
        }
    }

    private function editForm(model\User $user = null)
    {
        $user = new model\User($this->getParam(1));
        if ($user->getId()) {
            $this->tmplt->addData('content', new utils\Template("adm/user/form.html", [
                'caption' => 'Edit User ' . $user,
                'operation' => 'edit/' . $user->getId(),
                'name' => (string)$user->getName(),
                'surname' => (string)$user->getSurname(),
                'email' => (string)$user->getEmail(),
                'password-example' => utils\Secure::randPassword(),
            ]));
        } else {
            $this->tmplt->addData('content', new utils\Template("adm/message.html", [
                'type' => 'err',
                'message' => 'User to edit does not exist.'
            ]));
            $this->userTable();
        }
    }

    private function edit()
    {
        $user = new model\User($this->getParam(1));
        $user->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $user->setSurname(filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING));
        $user->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        if (filter_input(INPUT_POST, 'password')) {
            $user->setPassword(utils\Secure::hash(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING)));
        } else {
            $user->clrPassword();
        }
        $messageTmplt = new utils\Template("adm/message.html");
        $sameEmailUsers = (new db\Select())
            ->setSelect('id')
            ->setFrom('user')
            ->setWhere("id <> " . $user->getId() . " AND email = '" . $user->getEmail() . "'")
            ->run();
        if (empty($sameEmailUsers)) {
            if ($user->save()) {
                $messageTmplt->setAllData([
                    'message' => 'User ' . $user . ' has been saved.',
                    'type' => 'suc'
                ]);
            } else {
                $messageTmplt->setAllData([
                    'message' => 'User ' . $user . ' has not been saved. ' . db\MySQL::getLastError(),
                    'type' => 'err'
                ]);
            }
            $this->tmplt->addData('content', $messageTmplt);
            $this->userTable();
        } else {
            $messageTmplt->setAllData([
                'message' => 'Email ' . $user->getEmail() . ' already exists. User ' . $user . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->tmplt->addData('content', $messageTmplt);
            $this->editForm($user);
        }
    }

    private function deleteQuestion()
    {
        $user = new model\User($this->getParam(1));
        if ($user->getId()) {
            $deleteQuestionTmplt = new utils\Template("adm/user/delete-yes-no.html", [
                'id' => $user->getId(),
                'user' => (string)$user
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
        if ($user->getId()) {
            $messageTmplt->setAllData([
                'message' => "User $user has been deleted.",
                'type' => 'suc'
            ]);
            if (!$user->delete()) {
                $messageTmplt->setAllData([
                    'message' => "User $user has not been deleted.",
                    'type' => 'err'
                ]);
            }
        } else {
            $messageTmplt->setAllData([
                'message' => "User to delete does not exist.",
                'type' => 'err'
            ]);
        }
        $this->tmplt->setData('content', $messageTmplt);
        $this->userTable();
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
                    'email' => $record['email'],
                    'password' => (empty($record['password']) ? '&#10005;' : '&#10004;')
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->tmplt->addData('content', new utils\Template("adm/user/table.html", [
            'caption' => 'User List',
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
