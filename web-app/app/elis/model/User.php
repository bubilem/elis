<?php

namespace elis\model;

use elis\utils\db;
use elis\utils\db\Query;
use elis\utils\Secure;

/**
 * User model class
 * @version 0.0.1 201220 created
 */
class User extends Main
{

    public function __construct($pk = null)
    {
        if ($pk != null) {
            $this->load($pk);
        }
    }

    /**
     * Load record from database to model data
     *
     * @param mixed $pk
     * @return bool true if success, otherwise false
     */
    public function load($pk): bool
    {
        $result = (new db\Select())
            ->setSelect("*")->setFrom('user')->setWhere('id = ' . intval($pk))
            ->run();
        if (!empty($result[0]) && is_array($result[0])) {
            $this->data = $result[0];
            $result = (new db\Select())
                ->setSelect("role")->setFrom('user_has_role')->setWhere('user = ' . $this->getId())
                ->run();
            $roles = [];
            if (is_array($result) && !empty($result)) {
                foreach ($result as $role) {
                    $roles[] = $role['role'];
                }
            }
            $this->setRole($roles);
            return true;
        }
        return false;
    }

    /**
     * Save(update) model data to database record
     *
     * @return bool true if success, otherwise false
     */
    public function save(): bool
    {
        $data = [
            'email' => $this->getEmail(),
            'name' => $this->getName(),
            'surname' => $this->getSurname()
        ];
        if ($this->getPassword()) {
            $data['password'] = $this->getPassword();
        }
        if ($this->getId()) {
            if ((new db\Update('user', $data, $this->getId()))->run() !== false) {
                (new db\Delete('user_has_role', $this->getId()))->setAttribName('user')->run();
                foreach ($this->getRole() as $role) {
                    (new db\Insert('user_has_role', [
                        'user' => $this->getId(),
                        'role' => $role,
                        'assigned' => date("Y-m-d H:i:s")
                    ]))->run();
                }
                return true;
            }
        } else {
            if ($newId = (new db\Insert('user', $data))->run()) {
                $this->setId($newId);
                foreach ($this->getRole() as $role) {
                    (new db\Insert('user_has_role', [
                        'user' => $this->getId(),
                        'role' => $role,
                        'assigned' => date("Y-m-d H:i:s")
                    ]))->run();
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Delete the database record
     *
     * @return bool true if success, otherwise false
     */
    public function delete(): bool
    {
        if ($this->getId()) {
            if ((new db\Delete('user', $this->getId()))->run()) {
                $this->data = [];
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if user is in role
     *
     * @param string $role
     * @return bool true if user is in role
     */
    public function isInRole(string $role): bool
    {
        return is_array($this->getRole()) && in_array($role, $this->getRole());
    }

    /**
     * Login user
     *
     * @param string $email
     * @param string $password
     * @return bool success
     */
    public function login($email, $password)
    {
        if (!empty($email)) {
            $result = (new db\Select())
                ->setSelect("id")->setFrom('user')
                ->setWhere("email = '$email' AND password = '" . Secure::hash($password) . "'")
                ->run();
            if (!empty($result[0]['id']) && $this->load($result[0]['id'])) {
                $_SESSION['usr'] = $this->getId();
                return true;
            } else {
                return false;
            }
        }
        return null;
    }

    /**
     * Retain user from session or Logout user
     *
     * @return void
     */
    public function retainOrLogout($presenter)
    {
        if ($presenter->getParam(0) == 'logout') {
            $this->logout();
        } else if (
            $this->empty()
            && !empty($_SESSION['usr'])
            && $this->load(intval($_SESSION['usr']))
        ) {
            $_SESSION['usr'] = $this->getId();
        }
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout()
    {
        if (isset($_SESSION['usr'])) {
            unset($_SESSION['usr']);
        }
        $this->clearData();
    }

    public function empty()
    {
        return $this->getId() ? false : true;
    }

    public function __toString()
    {
        return trim(($this->getName() ? $this->getName() : '') . ' '
            . ($this->getSurname() ? $this->getSurname() : '') . ' '
            . ($this->getId() ? '[#' . $this->getId() . ']' : ''));
    }
}
