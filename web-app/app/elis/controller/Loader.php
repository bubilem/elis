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
        $filename = 'app/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($filename)) {
            require_once $filename;
        } else {
            throw new Exception("Unable to load $className.");
        }
    }
}
