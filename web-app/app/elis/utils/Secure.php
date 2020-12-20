<?php

namespace elis\utils;

/**
 * Secure utils class
 * @version 0.0.1 201220 created
 */
class Secure
{
    public static function geneHexaString($lenght = 8)
    {
        $chars = '0123456789abcdef';
        $hexaString = '';
        for ($i = 0; $i < $lenght; $i++) {
            $hexaString .= $chars[mt_rand(0, 15)];
        }
        return $hexaString;
    }
}
