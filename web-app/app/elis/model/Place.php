<?php

namespace elis\model;

use elis\utils\db;

/**
 * Place model class
 * @version 0.0.1 210125 created
 */
class Place extends Main
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
            ->setSelect("*")->setFrom('place')->setWhere('id = ' . intval($pk))
            ->run();
        if (isset($result[0]) && is_array($result[0]) && !empty($result[0])) {
            $this->data = $result[0];
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
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'street' => $this->getStreet(),
            'city_name' => $this->getCityName(),
            'city_code' => $this->getCityCode(),
            'country_code' => $this->getCountryCode(),
            'gps' => $this->getGps(),
        ];
        if ($this->getId()) {
            if ((new db\Update('place', $data, $this->getId()))->run() !== false) {
                return true;
            }
        } else {
            if ($newId = (new db\Insert('place', $data))->run()) {
                $this->setId($newId);
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
            if ((new db\Delete('place', $this->getId()))->run()) {
                $this->data = [];
                return true;
            }
        }
        return false;
    }

    public function __toString()
    {
        return trim($this->getName() . ' [' . $this->getCode() . ']');
    }
}
