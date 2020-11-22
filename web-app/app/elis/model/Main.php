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
    protected static $pkName = '';

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
    public abstract function del(): bool;

    /**
     * Set value by key to model data
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Get value by key from model data
     *
     * @param mixed $key
     * @return mixed
     */
    public function getData($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : false;
    }
}
