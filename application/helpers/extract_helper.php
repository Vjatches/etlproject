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
if(!function_exists('delete_all_between')) {
    function delete_all_between($string, $beginning, $end )
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
//Returns selectors for html elements (attributes) which we want to parse
if(!function_exists('getClassSelector')){
    function getClassSelector($attribute){
        switch($attribute){
            case 'title':
                $response['regular']='[class="bda14f76"]';
                $response['auction']='[class="bda14f76"]';
                break;
            case 'price':
                $response['regular']='[class="_9c03ab08 _64f505a9 b42b9cd5"]';
                $response['auction']='[class="_9c03ab08 _64f505a9 b42b9cd5"]';
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
