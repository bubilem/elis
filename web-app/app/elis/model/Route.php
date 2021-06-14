<?php

namespace elis\model;

use elis\utils\Arr;
use elis\utils\db;

/**
 * Route model class
 * @version 0.1.4 210614 getUsersInRoute, getLog
 * @version 0.0.1 210125 created
 */
class Route extends Main
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
        $result = (new db\Select())->setSelect("*")->setFrom('route')->setWhere('id = ' . intval($pk))->run();
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
            'begin' => $this->getBegin(),
            'end' => $this->getEnd(),
            'mileage' => $this->getMileage(),
            'vehicle' => $this->getVehicle(),
            'description' => $this->getDescription()
        ];
        if ($this->getId()) {
            if ((new db\Update('route', $data, $this->getId()))->run() !== false) {
                return true;
            }
        } else {
            if ($newId = (new db\Insert('route', $data))->run()) {
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
            if ((new db\Delete('route', $this->getId()))->run()) {
                $this->data = [];
                return true;
            }
        }
        return false;
    }

    /**
     * Get routes
     *
     * @param User $user
     * @return array
     */
    public static function getRoutes(User $user, array $roles): array
    {
        $query = (new db\Select())
            ->setSelect("r.*, MAX(e.date) laststatedate")
            ->addSelect("SUBSTRING_INDEX(GROUP_CONCAT(e.type ORDER BY e.date DESC),',',1) laststate")
            ->setFrom("route r LEFT JOIN event e ON r.id = e.route")
            ->setWhere("r.end is NULL")
            ->setGroup("r.id")
            ->setOrder("r.begin DESC");
        if (!$user->isInRole('ADM')) {
            $query->addFrom("JOIN route_has_user rhu ON r.id = rhu.route AND rhu.role IN(" . Arr::toStr($roles) . ")");
        }
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    /**
     * Get users in route
     *
     * @return array
     */
    public function getUsersInRoute(): array
    {
        $query = (new db\Select())
            ->setSelect("rhu.*, u.id, u.name, u.surname, u.email")
            ->setFrom("route_has_user rhu JOIN user u ON rhu.user = u.id")
            ->setWhere("rhu.route = " . $this->getId())
            ->setOrder("assigned");
        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    /**
     * Get Log
     *
     * @return array
     */
    public function getLog(): array
    {
        $query = (new db\Select())
            ->setSelect("e.*, CONCAT_WS(' ',u.name, u.surname) username")
            ->addSelect("CONCAT_WS(', ', IF(p.code IS NOT NULL, CONCAT(p.code,' (',CONCAT_WS(', ',p.city_name,p.country_code),')'),'OTH'), e.place_manual) placename")
            ->setFrom("event e LEFT JOIN user u ON e.recorded = u.id")
            ->addFrom("LEFT JOIN place p ON e.place = p.id")
            ->setWhere("e.route = " . $this->getId())
            ->setOrder('e.date DESC');

        $queryResult = $query->run();
        return is_array($queryResult) ? $queryResult : [];
    }

    public function __toString()
    {
        return trim($this->getName() . ' [#' . $this->getId() . ']');
    }
}
