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
     * Remove null values from data array
     *
     * @return self
     */
    public function remNullValues()
    {
        if (is_array($this->values) && !empty($this->values)) {
            foreach ($this->values as $aName => $aValue) {
                if ($aValue === null) {
                    unset($this->values[$aName]);
                }
            }
        }
        return $this;
    }

    /**
     * Render INSERT sql query to string
     *
     * @return string
     */
    public function toSql(): string
    {
        if (!$this->getTableName() || empty($this->values)) {
            return '';
        }
        $this->remNullValues();
        return "INSERT INTO " . $this->getTableName() . " (" .
            implode(", ", array_keys($this->values)) . ") VALUES ('" .
            implode("', '", array_values($this->values)) . "')";
    }
}
