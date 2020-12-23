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
     * Database table name
     *
     * @var string
     */
    protected $tableName;

    /**
     * Database attribute name
     *
     * @var string
     */
    protected $attribName;

    /**
     * Database attribute value
     *
     * @var string
     */
    protected $attribVal;

    /**
     * Attribute logical operation for records selection
     *
     * @var string
     */
    private $attribOper;

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
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * Set query string
     *
     * @param string $queryString  Query string
     * @return self
     */
    public function setQueryString(string $queryString)
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * Get arguments of query
     *
     * @return array
     */
    public function getArgs(): array
    {
        return $this->queryArgs;
    }

    /**
     * Set arguments of query
     *
     * @return self
     */
    public function setArgs(array $args)
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
     * @param string $tableName  
     * @return self
     */
    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Get database attribute name
     *
     * @return string
     */
    public function getAttribName(): string
    {
        return $this->attribName;
    }

    /**
     * Set database attribute name
     *
     * @param string $attribName  
     * @return self
     */
    public function setAttribName(string $attribName)
    {
        $this->attribName = $attribName;
        return $this;
    }

    /**
     * Get attribute value
     *
     * @return string
     */
    public function getAttribVal(): string
    {
        return $this->attribVal;
    }

    /**
     * Set attribute value
     *
     * @param string $attribVal  
     * @return self
     */
    public function setAttribVal(string $attribVal)
    {
        $this->attribVal = $attribVal;
        return $this;
    }

    /**
     * Get attribute logical operation
     *
     * @return string
     */
    public function getAttribOper(): string
    {
        return $this->attribOper;
    }

    /**
     * Set attribute logical operation
     *
     * @param string $attribVal  
     * @return self
     */
    public function setAttribOper(string $attribOper)
    {
        if (in_array($attribOper, ['=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE'])) {
            $this->attribOper = $attribOper;
        }
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
    function __toString(): string
    {
        return $this->toSql();
    }

    public function run()
    {
        if (MySQL::query($this) && !MySQL::getLastError()) {
            return true;
        }
        return false;
    }
}
