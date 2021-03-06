<?php

namespace elis\model;

use elis\utils\db;
use elis\utils\Secure;

/**
 * User model class
 * @version 0.1.4 210614 getUsers
 * @version 0.1.3 210613 isInRole update
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
     * Checks if is user last admin
     *
     * @return bool
     */
    public function isLastAdmin(): bool
    {
        $admins = (new db\Select())
            ->setSelect('user')
            ->setFrom('user_has_role')
            ->setWhere("role = 'ADM'")
            ->run();
        if (is_array($admins) && count($admins) == 1 && $admins[0]['user'] == $this->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Checks if user is in role
     *
     * @param string|array $roles
     * @return bool true if user is in role
     */
    public function isInRole($roles): bool
    {
        if (is_string($roles)) {
            return is_array($this->getRole()) && in_array($roles, $this->getRole());
        }
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (is_array($this->getRole()) && in_array($role, $this->getRole())) {
                    return true;
                }
            }
        }
        return false;
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

    /**
     * Get users
     *
     * @return array
     */
    public static function getUsers(): array
    {
        $query = (new db\Select())
            ->setSelect("u.*, GROUP_CONCAT(r.role SEPARATOR ' ') roles")
            ->setFrom("user u LEFT JOIN user_has_role r ON u.id = r.user")
            ->setGroup("u.id")
            ->setOrder('u.surname, u.name');
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    public function __toString()
    {
        return trim(($this->getName() ? $this->getName() : '') . ' '
            . ($this->getSurname() ? $this->getSurname() : '') . ' '
            . ($this->getId() ? '[#' . $this->getId() . ']' : ''));
    }
}
