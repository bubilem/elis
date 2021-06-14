<?php

namespace elis\model;

use elis\utils\Date;
use elis\utils\db;

/**
 * Package model class
 * @version 0.1.4 210614 getPackages, getLog
 * @version 0.0.1 210128 created
 */
class Package extends Main
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
            ->setSelect("*")->setFrom('package')->setWhere('id = ' . intval($pk))
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
            'code' => $this->getCode(),
            'type' => $this->getType(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'lenght' => $this->getLenght(),
            'weight' => $this->getWeight(),
            'description' => $this->getDescription()
        ];
        if ($this->getId()) {
            if ((new db\Update('package', $data, $this->getId()))->run() !== false) {
                return true;
            }
        } else {
            if ($newId = (new db\Insert('package', $data))->run()) {
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
            if ((new db\Delete('package', $this->getId()))->run()) {
                $this->data = [];
                return true;
            }
        }
        return false;
    }

    /**
     * Create new log
     *
     * @param string $state
     * @param Event $event
     * @return PackageLog
     */
    public function createLog(string $state, Event $event = null): PackageLog
    {
        if (!$this->getId()) {
            return null;
        }
        $log = new PackageLog();
        $log->setDate(Date::dbNow());
        $log->setPackage($this->getId());
        $log->setState($state);
        if ($event != null && $event->getId()) {
            $log->setEvent($event->getId());
        }
        return $log;
    }

    /**
     * Get Log
     *
     * @return array
     */
    public function getLog(): array
    {
        $query = (new db\Select())
            ->setSelect("l.*, e.type eventtype")
            ->setFrom("package_log l LEFT JOIN event e ON l.event = e.id")
            ->setWhere("l.package = " . $this->getId())
            ->setOrder('l.date DESC');
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    public static function getPackages(): array
    {
        $query = (new db\Select())
            ->setSelect("p.*, SUBSTRING_INDEX(GROUP_CONCAT(l.state ORDER BY l.date DESC),',',1) laststate")
            ->setFrom("package p JOIN package_log l ON p.id = l.package")
            ->setGroup('p.id')
            ->setOrder('p.id DESC');
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    public function __toString()
    {
        return trim($this->getCode() . ' [' . $this->getType() . ']');
    }
}
