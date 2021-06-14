<?php

namespace elis\model;

use elis\utils\db;

/**
 * Vehicle model class
 * @version 0.1.4 210614 getVehicles
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

    /**
     * Get vehicles
     *
     * @return array
     */
    public static function getVehicles(): array
    {
        $query = (new db\Select())
            ->setSelect("v.*, GROUP_CONCAT(CONCAT(r.name,' ',DATE_FORMAT(r.begin, '%Y-%m-%d')) ORDER BY r.begin DESC) route")
            ->setFrom("vehicle v LEFT JOIN route r ON v.id = r.vehicle AND r.begin < NOW() AND r.end IS NULL")
            ->setGroup("v.id")
            ->setOrder('v.name');
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    public function __toString()
    {
        return trim($this->getName() . ' [' . $this->getUid() . ']');
    }
}
