<?php

namespace elis\utils\db;

/**
 * SQL Select Query
 * @version 0.0.1 201128
 */
class Select extends Query
{

    /**
     * Code list of query parts 
     *
     * @var array
     */
    protected static $codeList = [
        'select' => 'SELECT',
        'from' => 'FROM',
        'where' => 'WHERE',
        'group' => 'GROUP BY',
        'having' => 'HAVING',
        'order' => 'ORDER BY',
        'limit' => 'LIMIT'
    ];

    /**
     * Parts of query
     *
     * @var array
     */
    protected $queryParts = [];

    /**
     * Dynamic get... set... add... or clr... method service
     *
     * @param string $name the name of the called method
     * @param array $arguments parameters of the called method
     * @return string|self string on get, otherwise self object
     */
    public function __call($name, $arguments)
    {
        $typeOfMethod = strtolower(substr($name, 0, 3));
        if (in_array($typeOfMethod, array('set', 'add', 'get', 'clr'))) {
            $name = strtolower(substr($name, 3));
            if (array_key_exists($name, self::$codeList)) {
                switch ($name) {
                    case 'select':
                    case 'order':
                    case 'group':
                        $separator = ', ';
                        break;
                    case 'where':
                    case 'having':
                        $separator = ' AND ';
                        break;
                    default:
                        $separator = ' ';
                }
                switch ($typeOfMethod) {
                    case 'set':
                        $value = implode($separator, $arguments);
                        if (!empty($value)) {
                            $this->queryParts[$name] = $value;
                        }
                        break;
                    case 'add':
                        $value = implode($separator, $arguments);
                        if (!empty($value)) {
                            $this->queryParts[$name] = (!empty($this->queryParts[$name]) ? $this->queryParts[$name] . $separator : '') . $value;
                        }
                        break;
                    case 'get':
                        return isset($this->queryParts[$name]) ? $this->queryParts[$name] : null;
                    case 'clr':
                        if (isset($this->queryParts[$name])) {
                            unset($this->queryParts[$name]);
                        }
                }
            }
        }
        return $this;
    }

    /**
     * Render parts of query to sql string
     *
     * @return string
     */
    function toSql(): string
    {
        $sql = '';
        foreach (self::$codeList as $key => $val) {
            if (!empty($this->queryParts[$key])) {
                $sql .= $val . ' ' . $this->queryParts[$key] . ' ';
            }
        }
        return trim($sql) . ';';
    }

    public function run()
    {
        if (!MySQL::query($this) || MySQL::getLastError()) {
            return false;
        }
        $result = MySQL::fetchAll();
        if (is_array($result)) {
            return $result;
        }
        return false;
    }
}
