<?php

namespace elis\utils;

/**
 * Secure utils class
 * @version 0.0.1 201220 created
 */
class Secure
{
    public static function randHexaString($lenght = 8)
    {
        $chars = '0123456789abcdef';
        $hexaString = '';
        for ($i = 0; $i < $lenght; $i++) {
            $hexaString .= $chars[mt_rand(0, 15)];
        }
        return $hexaString;
    }

    public static function randPassword($lenght = 8)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!?.+-';
        $password = '';
        for ($i = 0; $i < $lenght; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    public static function hash(string $input): string
    {
        return sha1($input);
    }
}
