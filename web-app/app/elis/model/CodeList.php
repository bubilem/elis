<?php

namespace elis\model;

/**
 * Code list class
 * @version 0.0.1 201128
 */
class CodeList
{

    /**
     * Code list items
     *
     * @var array
     */
    private $items;

    /**
     * Path to the code list directory
     *
     * @var string
     */
    private static $path = 'code-list/';

    /**
     * Constructor can load json file into items array
     *
     * @param string $filename
     */
    public function __construct(string $filename = null)
    {
    }

    /**
     * Load json file into items array
     *
     * @param string $filename
     * @return bool true when success, otherwise false
     */
    public function load(string $filename): bool
    {
        return false;
    }

    /**
     * Code list item/items getter
     *
     * @param string $firstKey
     * @param string $secondKey
     * @return mixed whole items array when no params, array item when key or keys used, null on fail
     */
    public function get(string $firstKey = null, string $secondKey = null)
    {
    }
}
