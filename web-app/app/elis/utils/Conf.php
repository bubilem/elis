<?php

namespace elis\utils;

/**
 * Configuration class
 * @version 0.0.1 201120
 */
class Conf
{

    private static $data = [];

    /**
     * Configuration ini file loading
     *
     * @param string $filename source conf file (path and filename)
     * @return bool true when success, otherwise false
     */
    public static function load(string $filename): bool
    {
        if (file_exists($filename)) {
            $data = parse_ini_file($filename);
            if (!empty($data) && is_array($data)) {
                self::$data = $data;
                return true;
            }
        }
        return false;
    }


    /**
     * Getting the configuration item by key
     * 
     * @param string $key configuration item key
     * @param string $defaultValue default value if item not found in conf file (array)
     * @return string 
     */
    public static function get(string $key, string $defaultValue = ''): string
    {
        return isset(self::$data[$key]) ? self::$data[$key] : $defaultValue;
    }
}
