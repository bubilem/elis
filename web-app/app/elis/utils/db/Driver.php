<?php

namespace elis\utils\db;

/**
 * Driver interface
 * @version 0.0.1 201128
 */
interface Driver
{
    /**
     * Connect to DBMS
     *
     * @return void
     */
    public static function connect();

    /**
     * Checks connection to DBMS
     *
     * @return bool true if connected, otherwise false
     */
    public static function isConnected(): bool;

    /**
     * Closes the connection to the database
     *
     * @return bool true on success, otherwise false
     */
    public static function close(): bool;

    /**
     * Handles sending a query to the database
     *
     * @param Query $query
     * @return mixed result
     */
    public static function query(Query $query);

    /**
     * Fetch the record from result
     *
     * @param mixed $result
     * @return mixed array of strings that corresponds to the fetched row or NULL if there are no more rows in resultset
     */
    public static function fetch($result = null);

    /**
     * Fetch all records from result to two-dimensional array
     *
     * @param mixed $result
     * @return array two-dimensional array on success
     */
    public static function fetchAll($result = null): array;

    /**
     * Gets last error message
     *
     * @return string
     */
    public static function getLastError(): string;
}
