<?php

namespace elis\utils;

/**
 * String utility
 * @version 0.0.1 210129 created
 */
class Str
{
    /**
     * Translate string to Camel Case form
     * first-example -> firstExample
     * this-second-example -> thisSecondExample
     *
     * @param string $value
     * @param string $separator
     * @param bool $lcfirst
     * @return string
     */
    public static function toCamelCase(string $value, string $separator = "-", bool $lcfirst = false): string
    {
        $str = str_replace($separator, '', ucwords($value, $separator));
        return $lcfirst ? lcfirst($str) : $str;
    }

    /**
     * Translate from Camel Case form to string
     * firstExample -> first-example
     * thisSecondExample -> this-second-example
     * 
     * @param string $value
     * @param string $separator
     * @return string
     */
    public static function fromCamelCase(string $value, string $separator = "-"): string
    {
        return strtolower(implode($separator, preg_split('/(?=[A-Z])/', lcfirst($value))));
    }
}
