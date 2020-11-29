<?php

namespace elis\utils\db;

/**
 * Query abstract class
 * @version 0.0.1 201128
 */
class Query
{

    /**
     * Query string
     *
     * @var string
     */
    protected $queryString;

    /**
     * Arguments of query
     *
     * @var array
     */
    protected $queryArgs;

    /**
     * Database primary key name
     *
     * @var string
     */
    protected $pkName;

    /**
     * Primary key value
     *
     * @var string
     */
    private $pkValue;

    /**
     * Database table name
     *
     * @var string
     */
    protected $tableName;

    public function __construct(string $queryString = '', array $queryArgs = [])
    {
        $this->setTableName('');
        $this->setQueryString($queryString);
        $this->setArgs($queryArgs);
    }

    /**
     * Get query string
     *
     * @return string
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Set query string
     *
     * @param string $queryString  Query string
     * @return self
     */
    public function setQueryString(string $queryString): Query
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * Get arguments of query
     *
     * @return array
     */
    function getArgs(): array
    {
        return $this->queryArgs;
    }

    /**
     * Set arguments of query
     *
     * @return self
     */
    function setArgs(array $args)
    {
        $this->queryArgs = $args;
        return $this;
    }

    /**
     * Get database table name
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Set database table name
     *
     * @param string $tableName  Database table name
     * @return self
     */
    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Get database primary key name
     *
     * @return string
     */
    public function getPkName(): string
    {
        return $this->pkName;
    }

    /**
     * Set database primary key name
     *
     * @param string $pkName  Database primary key name
     * @return self
     */
    public function setPkName(string $pkName)
    {
        $this->pkName = $pkName;
        return $this;
    }

    /**
     * Get primary key value
     *
     * @return string
     */
    public function getPkValue(): string
    {
        return $this->pkValue;
    }

    /**
     * Set primary key value
     *
     * @param string $pkValue  Primary key value
     * @return self
     */
    public function setPkValue(string $pkValue): Update
    {
        $this->pkValue = $pkValue;
        return $this;
    }



    /**
     * Render query sql string
     *
     * @return string
     */
    public function toSql(): string
    {
        return $this->queryString;
    }

    /**
     * Representation of an object into a string
     *
     * @return string
     */
    function __toString()
    {
        return $this->toSql();
    }
}
