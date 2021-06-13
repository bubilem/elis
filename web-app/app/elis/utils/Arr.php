<?php

namespace elis\utils;

/**
 * Array utility
 * @version 0.1.3 210613 created
 */
class Arr
{

    public static function toStr(array $arr, $sep = ',', $prefix = "'", $postfix = "'"): string
    {
        $str = '';
        foreach ($arr as $val) {
            if ($str) {
                $str = $sep . $str;
            }
            $str .= $prefix . $val . $postfix;
        }
        return $str;
    }
}
