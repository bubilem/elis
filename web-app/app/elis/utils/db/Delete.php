<?php

namespace elis\utils\db;

/**
 * SQL Delete Query
 * @version 0.0.1 201129
 */
class Delete extends Query
{

    public function __construct(string $tableName = '', string $pkValue = '')
    {
        $this->setTableName($tableName);
        $this->setPkName('id');
        $this->setPkValue($pkValue);
    }

    /**
     * Render delete sql query to string
     *
     * @return string
     */
    public function toSql(): string
    {
        return 'DELETE...';
    }
}
