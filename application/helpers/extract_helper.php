<?php
//Function which recursively converts multidimensional array to single-dimensional array for ease of iteration
if(!function_exists('toSingleArray')) {
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


//get final uri without parameters
if(!function_exists('getCleanUrl')) {
    function getCleanUrl($uri){
        if (strpos($uri, '&redirect=') !== false) {
            $url=get_string_between($uri,'redirect=','?');
        }
        else{
            $url = $uri;
        }
        return $url;
    }
}
if(!function_exists('get_string_between')) {
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
//Returns selectors for html elements (attributes) which we want to parse
if(!function_exists('getClassSelector')){
    function getClassSelector($attribute){
        switch($attribute){
            case 'title':
                $response['regular']='[class="_35c9aba1"]';
                $response['auction']='[class="_35c9aba1"]';
                break;
            case 'price':
                $response['regular']='[class="_55d3c43d _73e98a72 a1bdb080"]';
                $response['auction']='[class="_55d3c43d _73e98a72 a1bdb080"]';
                break;
            case 'seller':
                $response['regular']='[data-analytics-click-value="sellerLogin"]';
                $response['auction']='[data-analytics-click-value="sellerLogin"]';
                break;
            default:
                $response['regular']='';
                $response['auction']='';
                break;
        }
        return $response;
    }
}
