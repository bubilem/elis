<?php

namespace elis\model;

use elis\utils\db;

/**
 * Vehicle model class
 * @version 0.0.1 210102 created
 */
class Vehicle extends Main
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
            ->setSelect("*")->setFrom('vehicle')->setWhere('id = ' . intval($pk))
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
            'uid' => $this->getUid(),
            'mileage' => $this->getMileage(),
            'avg_consuption' => $this->getAvgConsuption()
        ];
        if ($this->getId()) {
            if ((new db\Update('vehicle', $data, $this->getId()))->run() !== false) {
                return true;
            }
        } else {
            if ($newId = (new db\Insert('vehicle', $data))->run()) {
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
            if ((new db\Delete('vehicle', $this->getId()))->run()) {
                $this->data = [];
                return true;
            }
        }
        return false;
    }

    public function __toString()
    {
        return trim($this->getName() . ' [' . $this->getUid() . ']');
    }
}
