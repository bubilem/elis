<?php

namespace elis\model;

use elis\utils\db;
use elis\utils\db\Insert;

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
        $query = (new db\Select())->setSelect("*")->setFrom('user')->setWhere('id = ' . intval($pk));
        if (!db\MySQL::query($query) || db\MySQL::getLastError()) {
            return false;
        }
        $result = db\MySQL::fetch();
        if (is_array($result) && !empty($result)) {
            $this->data = $result;
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
            'password' => $this->getPassword(),
            'name' => $this->getName(),
            'surname' => $this->getSurname()
        ];
        if ($this->getId()) {
            $query = new db\Update('user', $data, $this->getId());
        } else {
            $query = new db\Insert('user', $data);
        }
        if (db\MySQL::query($query) && !db\MySQL::getLastError()) {
            if ($query instanceof Insert) {
                $this->setId(db\MySQL::getLastInsertId());
            }
            return true;
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
            $query = new db\Delete('user', $this->getId());
            if (db\MySQL::query($query) && !db\MySQL::getLastError()) {
                $this->data = [];
                return true;
            }
        }
        return false;
    }
}
