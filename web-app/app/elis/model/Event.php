<?php

namespace elis\model;

use elis\utils\Date;
use elis\utils\db;

/**
 * Event model class
 * @version 0.1.2 210612 created
 */
class Event extends Main
{

    public function __construct($type = null)
    {
        $this->setDate(Date::dbNow());
        if ($type != null) {
            $this->setType($type);
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
            'type' => $this->getType(),
            'recorded' => $this->getRecorded(),
            'route' => $this->getRoute(),
            'place' => $this->getPlace(),
            'place_manual' => $this->getPlaceManual(),
            'description' => $this->getDescription()
        ];
        if ($this->getId()) {
            if ((new db\Update('event', $data, $this->getId()))->run() !== false) {
                return true;
            }
        } else {
            if ($newId = (new db\Insert('event', $data))->run()) {
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

        return false;
    }

    public function __toString()
    {
        return trim($this->getType() . ' [' . $this->getDate() . ']');
    }
}
