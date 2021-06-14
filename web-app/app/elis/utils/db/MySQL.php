<?php

namespace elis\utils\db;

use elis\utils\Conf;

/**
 * MySQL driver
 * @version 0.0.1 201122
 */
class MySQL implements Driver
{
    /**
     *  Object which represents the connection to a MySQL Server, otherwise null.
     *
     * @var mixed
     */
    private static $link = null;

    /**
     * Mysqli result on success query, false on error, otherwise null
     *
     * @var mixed
     */
    private static $lastResult = null;

    /**
     * Last error message
     *
     * @var string
     */
    private static $lastError = '';

    /**
     * Connect to MySQL DBMS
     *
     * @return void
     */
    public static function connect()
    {
        if ((self::$link = @mysqli_connect(
            Conf::get("DB_HOST"),
            Conf::get("DB_USER"),
            Conf::get("DB_PASS"),
            Conf::get("DB_DTBS")
        )) !== false) {
            self::query(new Query("SET CHARACTER SET UTF8"));
        } else {
            self::$link = null;
        }
    }

    /**
     * Checks connection to DBMS
     *
     * @return bool true if connected, otherwise false
     */
    public static function isConnected(): bool
    {
        return self::$link !== null;
    }

    /**
     * Closes the connection to the database
     *
     * @return bool true on success, otherwise false
     */
    public static function close(): bool
    {
        return mysqli_close(self::$link);
    }

    /**
     * Handles sending a query to the database
     *
     * @param Query $query
     * @return mixed result
     */
    public static function query(Query $query)
    {
        if (!self::isConnected()) {
            self::connect();
        }
        self::$lastResult = mysqli_query(self::$link, $query->toSql());
        if (self::$lastResult === false) {
            self::$lastError = mysqli_error(self::$link);
        } else {
            self::$lastError = '';
        }
        return self::$lastResult;
    }

    /**
     * Fetch the record from result
     *
     * @param mixed $result
     * @return mixed array of strings that corresponds to the fetched row or NULL if there are no more rows in resultset
     */
    public static function fetch($result = null)
    {
        return mysqli_fetch_array($result != null ? $result : self::$lastResult, MYSQLI_ASSOC);
    }

    /**
     * Fetch all records from result to two-dimensional array
     *
     * @param mixed $result
     * @return array two-dimensional array on success
     */
    public static function fetchAll($result = null): array
    {
        $array = [];
        while ($record = self::fetch($result != null ? $result : self::$lastResult)) {
            $array[] = $record;
        }
        return $array;
    }

    /**
     * Gets last error message
     *
     * @return string
     */
    public static function getLastError(): string
    {
        return self::$lastError;
    }

    /**
     * Returns last insert id
     *
     * @return string
     */
    public static function getLastInsertId(): string
    {
        return mysqli_insert_id(self::$link);
    }

    /**
     * Returns the number of rows affected by the last query
     *
     * @return int
     */
    public static function getAffectedRows(): int
    {
        return mysqli_affected_rows(self::$link);
    }
}
