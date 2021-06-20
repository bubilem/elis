<?php

namespace elis\model;

use elis\utils\Date;
use elis\utils\db;

/**
 * Package model class
 * @version 0.2.0 210619 event and route info in getPackages, getLastLog()
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
    public function getLogList(): array
    {
        $query = (new db\Select())
            ->setSelect("l.*, e.type eventtype")
            ->addSelect("IF(e.place IS NOT NULL,CONCAT_WS(', ',p.code),e.place_manual) placename")
            ->addSelect("r.name routename, r.id routeid")
            ->setFrom("package_log l LEFT JOIN event e ON l.event = e.id")
            ->addFrom("LEFT JOIN place p ON p.id = e.place")
            ->addFrom("LEFT JOIN route r ON r.id = e.route")
            ->setWhere("l.package = " . $this->getId())
            ->setOrder('l.date DESC');
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }


    public function getLastLog(): PackageLog
    {
        $query = (new db\Select())
            ->setSelect("id")
            ->setFrom("package_log")
            ->setWhere("package = " . $this->getId())
            ->setOrder("date DESC")
            ->setLimit("1");
        $queryResult = $query->run();
        return empty($queryResult[0]['id']) ? null : new PackageLog($queryResult[0]['id']);
    }

    public static function getPackages(): array
    {
        $query = (new db\Select())
            ->setSelect("p.*, CONCAT_WS('-',p.code, p.type) name")
            ->addSelect("SUBSTRING_INDEX(GROUP_CONCAT(CONCAT_WS('|',CONCAT_WS('-',l.state,e.type,r.name), r.id) ORDER BY l.date DESC),',',1) laststate")
            ->setFrom("package p LEFT JOIN package_log l ON p.id = l.package")
            ->addFrom("LEFT JOIN event e ON l.event = e.id")
            ->addFrom("LEFT JOIN route r ON e.route = r.id")
            ->setGroup('p.id')
            ->setOrder('p.id DESC');
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    public static function getPackagesToLoad(): array
    {
        $query = (new db\Select())
            ->setSelect("p.id, p.code, p.type, CONCAT_WS('-',p.code, p.type) name")
            ->addSelect("SUBSTRING_INDEX(GROUP_CONCAT(l.state ORDER BY l.date DESC),',',1) laststate")
            ->setFrom("package p JOIN package_log l ON p.id = l.package")
            ->setHaving("laststate = 'WTG'")
            ->setGroup("p.id");
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    public function __toString()
    {
        return trim($this->getCode() . ' [' . $this->getType() . ']');
    }
}
