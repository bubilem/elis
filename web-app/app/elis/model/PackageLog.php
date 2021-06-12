<?php

namespace elis\model;

use elis\utils\db;

/**
 * Package Log model class
 * @version 0.1.2  created
 */
class PackageLog extends Main
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
            ->setSelect("*")->setFrom('package_log')->setWhere('id = ' . intval($pk))
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
            'date' => $this->getDate(),
            'package' => $this->getPackage(),
            'state' => $this->getState(),
            'event' => $this->getEvent()

        ];
        if ($this->getId()) {
            if ((new db\Update('package_log', $data, $this->getId()))->run() !== false) {
                return true;
            }
        } else {
            if ($newId = (new db\Insert('package_log', $data))->run()) {
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
            if ((new db\Delete('package_log', $this->getId()))->run()) {
                $this->data = [];
                return true;
            }
        }
        return false;
    }

    public function __toString()
    {
        return trim($this->getCode() . ' [pck:' . $this->getPackage() . ',  sta:' . $this->getState() . ', dat:' . $this->getDate() . ']');
    }
}
