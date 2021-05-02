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
        $this->pageTmplt->setData('title', 'User Administration');
    }

    protected function newForm($model = null)
    {
        $roles = new model\CodeList("user-roles.json");
        $checkboxTmplt = new utils\Template("adm/user/role-checkbox.html");
        $checkboxes = '';
        foreach ($roles->getItems() as $role) {
            $checkboxTmplt->clearData()->setAllData([
                'code' => $role->getCode(),
                'name' => $role->getName(),
                'desc' => $role->getDesc(),
                'checked' => ($model instanceof model\User && $model->isInRole($role->getCode()) ? 'checked' : '')
            ]);
            $checkboxes .= $checkboxTmplt;
        }
        $this->adminTmplt->addData('content', new utils\Template("adm/user/form.html", [
            'caption' => 'New User',
            'operation' => 'new',
            'name' => $model instanceof model\User ? $model->getName() : '',
            'surname' => $model instanceof model\User ? $model->getSurname() : '',
            'email' => $model instanceof model\User ? $model->getEmail() : '',
            'password-example' => utils\Secure::randPassword(),
            'role-checkboxes' => $checkboxes
        ]));
    }

    protected function new()
    {
        $user = new model\User();
        $user->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $user->setSurname(filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING));
        $user->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        if (filter_input(INPUT_POST, 'password')) {
            $user->setPassword(utils\Secure::hash(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING)));
        }
        $roles = new model\CodeList("user-roles.json");
        $checkedRoles = [];
        foreach ($roles->getItems() as $role) {
            if (filter_input(INPUT_POST, 'role_' . $role->getCode())) {
                $checkedRoles[] = $role->getCode();
            }
        }
        $user->setRole($checkedRoles);
        $messageTmplt = new utils\Template("other/message.html");
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
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setData('message', 'Email ' . $user->getEmail() . ' already exists. New user ' . $user . ' has not been created.');
            $messageTmplt->setData('type', 'war');
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->newForm($user);
        }
    }

    protected function editForm($model = null)
    {
        $model = new model\User($this->getParam(2));
        if ($model->getId()) {
            $roles = new model\CodeList("user-roles.json");
            $checkboxTmplt = new utils\Template("adm/user/role-checkbox.html");
            $checkboxes = '';
            foreach ($roles->getItems() as $role) {
                $checkboxTmplt->clearData()->setAllData([
                    'code' => $role->getCode(),
                    'name' => $role->getName(),
                    'desc' => $role->getDesc(),
                    'checked' => ($model->isInRole($role->getCode()) ? 'checked' : '')
                ]);
                $checkboxes .= $checkboxTmplt;
            }
            $this->adminTmplt->addData('content', new utils\Template("adm/user/form.html", [
                'caption' => 'Edit User ' . $model,
                'operation' => 'edit/' . $model->getId(),
                'name' => (string)$model->getName(),
                'surname' => (string)$model->getSurname(),
                'email' => (string)$model->getEmail(),
                'password-example' => utils\Secure::randPassword(),
                'role-checkboxes' => $checkboxes
            ]));
        } else {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'User to edit does not exist.'
            ]));
            $this->table();
        }
    }

    protected function edit()
    {
        $user = new model\User($this->getParam(2));
        $user->setName(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
        $user->setSurname(filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_STRING));
        $user->setEmail(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        if (filter_input(INPUT_POST, 'password')) {
            $user->setPassword(utils\Secure::hash(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING)));
        } else {
            $user->clrPassword();
        }
        $roles = new model\CodeList("user-roles.json");
        $checkedRoles = [];
        foreach ($roles->getItems() as $role) {
            if (filter_input(INPUT_POST, 'role_' . $role->getCode())) {
                $checkedRoles[] = $role->getCode();
            }
        }
        $user->setRole($checkedRoles);
        $messageTmplt = new utils\Template("other/message.html");
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
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->table();
        } else {
            $messageTmplt->setAllData([
                'message' => 'Email ' . $user->getEmail() . ' already exists. User ' . $user . ' has not been saved.',
                'type' => 'war'
            ]);
            $this->adminTmplt->addData('content', $messageTmplt);
            $this->editForm($user);
        }
    }

    protected function deleteQuestion()
    {
        $user = new model\User($this->getParam(2));
        if ($user->getId()) {
            $deleteQuestionTmplt = new utils\Template("adm/user/delete-yes-no.html", [
                'id' => $user->getId(),
                'user' => (string)$user
            ]);
            $this->adminTmplt->addData('content', $deleteQuestionTmplt);
        } else {
            $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                'type' => 'err',
                'message' => 'User to delete does not exist.'
            ]));
            $this->table();
        }
    }

    protected function delete()
    {
        $user = new model\User($this->getParam(2));
        $messageTmplt = new utils\Template("other/message.html");
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
        $this->adminTmplt->setData('content', $messageTmplt);
        $this->table();
    }

    protected function table()
    {
        $tableRowTmplt = new utils\Template("adm/user/table-row.html");
        $rows = '';
        $query = (new db\Select())
            ->setSelect("u.*, GROUP_CONCAT(r.role SEPARATOR ' ') roles")
            ->setFrom("user u LEFT JOIN user_has_role r ON u.id = r.user")
            ->setGroup("u.id")
            ->setOrder('u.surname, u.name');
        $queryResult = $query->run();
        if (is_array($queryResult)) {
            foreach ($queryResult as $record) {
                $tableRowTmplt->clearData()->setAllData([
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'surname' => $record['surname'],
                    'email' => $record['email'],
                    'password' => (empty($record['password']) ? '&#10005;' : '&#10004;'),
                    'roles' => $record['roles']
                ]);
                $rows .= $tableRowTmplt;
            }
        }
        $this->adminTmplt->addData('content', new utils\Template("adm/user/table.html", [
            'caption' => 'User List',
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
