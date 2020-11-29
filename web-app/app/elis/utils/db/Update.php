<?php

namespace elis\utils\db;

/**
 * SQL Update Query
 * @version 0.0.1 201129
 */
class Update extends Query
{

    /**
     * Update values array
     * Item key is attribute name. Item value is attribute value.
     *
     * @var array
     */
    private $values;

    public function __construct(string $tableName = '', array $values = [], string $pkValue = '')
    {
        $this->setTableName($tableName);
        $this->setPkName('id');
        $this->setPkValue($pkValue);
        $this->setValues($values);
    }

    /**
     * Get the value of values
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Set the value of values
     *
     * @param array $values
     * @return self
     */
    public function setValues(array $values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Render update sql query to string
     *
     * @return string
     */
    public function toSql(): string
    {
        return 'UPDATE...';
    }
}
