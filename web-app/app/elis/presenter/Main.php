<?php

namespace elis\presenter;

use elis\utils\db;

/**
 * Main presenter
 * @version 0.0.1 201121 created
 */
abstract class Main
{

    /**
     * Uri params
     *
     * @var array
     */
    protected $params;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (db\MySQL::isConnected()) {
            db\MySQL::close();
        }
    }

    /**
     * Get param value by index
     *
     * @param int $index
     * @return mixed string on success, otherwise false
     */
    public function getParam(int $index)
    {
        return isset($this->params[$index]) ? $this->params[$index] : false;
    }

    /**
     * Abstract method run
     * Every presenter must have it
     *
     * @return void
     */
    abstract public function run();
}
