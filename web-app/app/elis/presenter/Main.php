<?php

namespace elis\presenter;

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
