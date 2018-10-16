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

//Returns selectors for html elements (attributes) which we want to parse
if(!function_exists('getClassSelector')){
    function getClassSelector($attribute){
        switch($attribute){
            case 'title':
                $response['regular']='[class="_884d145b"]';
                $response['auction']='[class="m-heading m-heading--xs si-title"]';
                break;
            case 'price':
                $response['regular']='[class="_1f306df3 _1c943cca d7cfa755"]';
                $response['auction']='[class="m-price m-price--primary"]';
                break;
            case 'seller':
                $response['regular']='[class="_28bad9f5 e42e4878 _808f2003"]';
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