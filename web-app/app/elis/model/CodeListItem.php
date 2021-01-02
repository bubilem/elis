<?php

namespace elis\model;

/**
 * Code list item class
 * @version 0.0.1 201230
 */
class CodeListItem
{

    /**
     * Code list item code
     *
     * @var string
     */
    private $code;

    /**
     * Code list item data
     *
     * @var array
     */
    private $data;

    public function __construct(string $code, array $data)
    {
        $this->code = $code;
        $this->data = $data;
    }

    /**
     * Item code getter
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Dynamic item data getter
     *
     * @param string $name the name of the called method
     * @param array $arguments parameters of the called method
     * @return string string on get, null if not found
     */
    public function __call($name, $arguments)
    {
        $typeOfMethod = strtolower(substr($name, 0, 3));
        $name = strtolower(substr($name, 3));
        if ($typeOfMethod == 'get' && isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }
}
