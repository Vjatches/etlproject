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
                $response['regular']='[class="_2b138ce8"]';
                $response['auction']='[class="m-heading m-heading--xs si-title"]';
                break;
            case 'price':
                $response['regular']='[class="_09c415b5 _4037db5f ae780ebb"]';
                $response['auction']='[class="m-price m-price--primary"]';
                break;
            case 'seller':
                $response['regular']='[data-analytics-click-value="sellerLogin"]';
                $response['auction']='[class="m-link"]';
                break;
            default:
                $response['regular']='';
                $response['auction']='';
                break;
        }
        return $response;
    }
}