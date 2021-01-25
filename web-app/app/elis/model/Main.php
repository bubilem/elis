<?php

namespace elis\model;

/**
 * Main model class
 * @version 0.0.1 201122 created
 */
abstract class Main
{

    /**
     * All model db data 
     *
     * @var array
     */
    protected $data = [];

    /**
     * Database primary key name
     *
     * @var string
     */
    protected static $pkName = 'id';

    /**
     * Database table name
     *
     * @var string
     */
    protected static $tableName = '';

    /**
     * Load record from database to model data
     *
     * @param mixed $pk
     * @return bool true if success, otherwise false
     */
    public abstract function load($pk): bool;

    /**
     * Save(update) model data to database record
     *
     * @return bool true if success, otherwise false
     */
    public abstract function save(): bool;

    /**
     * Delete the database record
     *
     * @return bool true if success, otherwise false
     */
    public abstract function delete(): bool;

    /**
     * Set value by key to model data
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function setData($key, $value)
    {
        if ($value !== null) {
            $this->data[$key] = $value;
        } else {
            unset($this->data[$key]);
        }
    }

    /**
     * Get value by key from model data
     *
     * @param mixed $key
     * @return mixed
     */
    public function getData($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Dynamic get... set... clr... method service
     *
     * @param string $name the name of the called method
     * @param array $arguments parameters of the called method
     * @return string|false|self string on get, false on error, otherwise self object
     */
    public function __call($name, $arguments)
    {
        $typeOfMethod = strtolower(substr($name, 0, 3));
        $name = lcfirst(substr($name, 3));
        switch ($typeOfMethod) {
            case 'set':
                if (isset($arguments[0])) {
                    $this->setData(self::modelToDbName($name), $arguments[0]);
                }
                return $this;
            case 'clr':
                $this->setData(self::modelToDbName($name), null);
                return $this;
            case 'get':
                return $this->getData(self::modelToDbName($name));
        }
        return false;
    }

    /**
     * Translate attribute database name to model name
     *
     * @param string $dbName
     * @return string model name
     */
    public static function dbToModelName(string $dbName): string
    {
        return str_replace('_', '', lcfirst(ucwords($dbName, '_')));
    }

    /**
     * Translate attribute model name to database name
     *
     * @param string $modelName
     * @return string database name
     */
    public static function modelToDbName(string $modelName): string
    {
        return strtolower(implode('_', preg_split('/(?=[A-Z])/', $modelName)));
    }
}
