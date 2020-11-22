<?php

namespace elis\controller;

/**
 * Exception handler
 * @version 0.0.1 201121 created
 */
class Exception extends \Exception
{
    /**
     * Exception message handler
     * @param Exception $e
     */
    public static function exceptionHandler($e)
    {
        echo '<pre><strong>Exception:</strong> ' . $e->getMessage() . '</pre>';
    }
}
