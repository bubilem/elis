<?php

namespace elis\utils\db;

/**
 * SQL Insert Query
 * @version 0.0.1 201129
 */
class Insert extends Query
{

    /**
     * Insert values array
     * Item key is attribute name. Item value is attribute value.
     *
     * @var array
     */
    private $values;

    public function __construct(string $tableName = '', array $values = [])
    {
        $this->setTableName($tableName);
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
     * Render insert sql query to string
     *
     * @return string
     */
    public function toSql(): string
    {
        return 'INSERT...';
    }
}
