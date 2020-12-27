<?php

namespace elis\utils;

/**
 * Template class
 * @version 0.0.1 201127
 */
class Template
{
    /**
     * Opening and closing marks for data
     *
     * @var array
     */
    private static $tags = ['{', '}'];

    /**
     * Template path prefix
     *
     * @var string
     */
    private static $pathPrefix = "app/elis/template/";

    /**
     * Template filename
     *
     * @var string
     */
    private $filename;

    /**
     * Data for template
     *
     * @var array
     */
    private $data;

    /**
     * Template constructor
     *
     * @param string $filename
     * @param array $data
     */
    public function __construct(string $filename, array $data = [])
    {
        if (!empty($filename) && file_exists(self::$pathPrefix . $filename)) {
            $this->filename =  $filename;
        }
        $this->setAllData($data);
    }

    /**
     * Set all data in one step
     *
     * @param array $data
     * @return Template
     */
    public function setAllData(array $data): Template
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the data value according to the key
     *
     * @param string $key
     * @param mixed $value
     * @return Template
     */
    public function setData(string $key, $value): Template
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Add the data value according to the key
     *
     * @param string $key
     * @param mixed $value
     * @return Template
     */
    public function addData(string $key, $value): Template
    {
        if (isset($this->data[$key])) {
            $this->data[$key] .= $value;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Clear all template data
     *
     * @return Template
     */
    public function clearData(): Template
    {
        $this->data = [];
        return $this;
    }

    /**
     * Insert data into the template and generate a string
     * 
     * @return string
     */
    public function render(): string
    {
        if (empty($this->filename)) {
            return '';
        }
        $content = file_get_contents(self::$pathPrefix . $this->filename);
        foreach ($this->data as $key => $val) {
            $content = str_replace(self::$tags[0] . $key . self::$tags[1], $val, $content);
        }
        return $content;
    }

    /**
     * String representation of the Template
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
