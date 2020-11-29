<?php

namespace elis\controller;

/**
 * Autoloader (require) the needed class
 * @version 0.0.1 201121 created
 */
class Loader
{

    /**
     * Automatic loads the class files
     *
     * @param string $className
     * @return void
     * @throws Exception if unable to load class file
     */
    public static function loadClass(string $className)
    {
        $parts = explode('\\', $className);
        $filename = self::makeFirstLettersUpperCase(array_pop($parts)) . '.php';
        $path = 'app/' . implode('/', $parts) . '/';
        if (file_exists($path . $filename)) {
            require_once $path . $filename;
        } else {
            throw new Exception("Unable to load $className.");
        }
    }

    /**
     * The first letters of words separated by a hyphen are changed to uppercase
     *
     * @param string $value
     * @return string
     */
    public static function makeFirstLettersUpperCase(string $value): string
    {
        return str_replace('-', '', ucwords($value, '-'));
    }
}
