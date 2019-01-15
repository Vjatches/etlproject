<?php

if (!function_exists('toSingleArray')) {
    /**
     * Function which recursively converts multidimensional array to one-dimensional array for ease of iteration
     * @param $arr - input multidimensional array
     * @return array - output one-dimensional array
     */
    function toSingleArray($arr)
    {
        foreach ($arr as $key) {
            if (is_array($key)) {
                $arr1 = toSingleArray($key);
                foreach ($arr1 as $k) {
                    $new_arr[] = $k;
                }
            } else {
                $new_arr[] = $key;
            }
        }
        return $new_arr;
    }
}
if (!function_exists('getCleanUrl')) {
    /**
     * Strip uri from redirects and any parameters
     * @param $uri - input dirty uri
     * @return bool|string - clean uri or error
     */
    function getCleanUrl($uri)
    {
        if (strpos($uri, '&redirect=') !== false) {
            $url = get_string_between($uri, 'redirect=', '?');
        } else {
            $url = $uri;
        }
        return $url;
    }
}
if (!function_exists('get_string_between')) {
    /**
     * Function which returns substring located between two other substrings
     * @param $string - input string
     * @param $start - start substring
     * @param $end - end substring
     * @return bool|string - cut substring or error
     */
    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
if (!function_exists('delete_all_between')) {
    /**
     * Cuts and removes substring between two other substrings
     * @param $string - input string
     * @param $beginning - start substring
     * @param $end - end substring
     * @return mixed - transformed input substring
     */
    function delete_all_between($string, $beginning, $end)
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return delete_all_between($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
    }
}
if (!function_exists('getTime')) {
    /**
     * Function which returns time between two microtimes in seconds
     * if it is less than 60, otherwise in minutes and seconds
     * @param $start_time
     * @param $end_time
     * @return string
     */
    function getTime($start_time, $end_time)
    {
        $duration = $end_time-$start_time;

        if ($duration < 60)
           return $duration;
        else {
            $min = (int)($duration / 60);
            $sec = $duration % 60;
           return "$min min $sec s";
        }
    }

}
