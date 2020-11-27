<?php

namespace elis\utils;

/**
 * Template class
 * @version 0.0.1 201127
 */
class Template
{
    private static $tags = ['{', '}'];

    private static $pathPrefix = "app/elis/template/";

    private $filename;

    private $data;

    public function __construct(string $filename)
    {
        $this->data = [];
        if (!empty($filename) && file_exists(self::$pathPrefix . $filename)) {
            $this->filename =  $filename;
        }
    }

    public function setData(string $key, $value)
    {
        $this->data[$key] = $value;
    }


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
}
