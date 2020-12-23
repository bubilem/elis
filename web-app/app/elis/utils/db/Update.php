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

    public function __construct(string $tableName = '', array $values = [], string $attribVal = '', string $attribOper = '=')
    {
        $this->setTableName($tableName);
        $this->setAttribName('id');
        $this->setAttribVal($attribVal);
        $this->setAttribOper($attribOper);
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
        if (!$this->getTableName() || !$this->getValues() || !$this->getAttribName() || !$this->getAttribVal()) {
            return '';
        }
        $values = [];
        foreach ($this->getValues() as $aName => $aVal) {
            $values[] = $aName . " = '" . $aVal . "'";
        }
        $whereCondition = '';
        if ($this->getAttribName() && $this->getAttribOper() && $this->getAttribVal()) {
            $whereCondition = $this->getAttribName() . " " . $this->getAttribOper() .
                " '" . $this->getAttribVal() . "'";
        }
        return "UPDATE " . $this->getTableName() . " SET " .
            implode(", ", array_values($values)) .
            ($whereCondition ? " WHERE " . $whereCondition : "");
    }

    public function run()
    {
        if (MySQL::query($this) && !MySQL::getLastError()) {
            return MySQL::getAffectedRows();
        }
        return false;
    }
}
