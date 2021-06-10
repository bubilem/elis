<?php

namespace elis\utils;

/**
 * Date utility
 * @version 0.0.1 210609 created
 */
class Date
{
    public static function dbNow(): string
    {
        return date("Y-m-d H:i:s");
    }
}
