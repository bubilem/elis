<?php

namespace elis\utils\db;

/**
 * SQL Delete Query
 * @version 0.0.1 201129
 */
class Delete extends Query
{

    public function __construct(string $tableName = '', string $attribVal = '', string $attribOper = '=')
    {
        $this->setTableName($tableName);
        $this->setAttribName('id');
        $this->setAttribVal($attribVal);
        $this->setAttribOper($attribOper);
    }

    /**
     * Render delete sql query to string
     *
     * @return string
     */
    public function toSql(): string
    {
        if (!$this->getTableName()) {
            return '';
        }
        $whereCondition = '';
        if ($this->getAttribName() && $this->getAttribOper() && $this->getAttribVal()) {
            $whereCondition = $this->getAttribName() . " " . $this->getAttribOper() .
                " '" . $this->getAttribVal() . "'";
        }
        return "DELETE FROM " . $this->getTableName() . ($whereCondition ? " WHERE " . $whereCondition : "");
    }
}
