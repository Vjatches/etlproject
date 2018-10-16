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
        //Getting final url, most usefull when uri contains redirects
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $a = curl_exec($ch); // $a will contain all headers
        //Stripping query string from final url
        $url = strtok(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),'?');
        return $url;
    }
}