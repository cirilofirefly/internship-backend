<?php

if (!function_exists('split_name')) {
    function split_name($string)
    {
        $arr = explode(' ', $string);
        $num = count($arr);
        $first_name = $middle_name = $last_name = null;
        if ($num == 2) {
            list($first_name, $last_name) = $arr;
        } else {
            list($first_name, $middle_name, $last_name) = $arr;
        }
        return (empty($first_name) || $num > 3) ? false : compact(
            'first_name', 'middle_name', 'last_name'
        );
    }
}

if (!function_exists('minutes_to_decimal')) {
    function minutes_to_decimal($minutes = 0)
    {
        return (float) round($minutes / 60, 2);
    }
}

?>