<?php

namespace elis\model;

use elis\utils\db;

/**
 * Route has User model class
 * @version 0.0.1 210609 created
 */
class RouteHasUser extends Main
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
        $result = (new db\Select())->setSelect("*")
            ->setFrom('route_has_user')
            ->setWhere('route = ' . intval($pk['route']) . ' AND user = ' . intval($pk['user']))->run();
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
            'route' => $this->getRoute(),
            'user' => $this->getUser(),
            'role' => $this->getRole(),
            'assigned' => $this->getAssigned()
        ];
        (new db\Insert('route_has_user', $data))->run();
        return true;
    }

    /**
     * Delete the database record
     *
     * @return bool true if success, otherwise false
     */
    public function delete(): bool
    {
        if ($this->getRoute() && $this->getUser()) {
            if ((new db\Query('DELETE FROM route_has_user WHERE route = ' . $this->getRoute() . ' && user = ' . $this->getUser()))->run()) {
                $this->data = [];
                return true;
            }
        }
        return false;
    }

    public function __toString()
    {
        return trim('RHU[R' . $this->getRoute() . ',U' . $this->getUser() . ']');
    }
}
