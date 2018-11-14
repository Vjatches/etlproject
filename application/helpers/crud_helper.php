<?php

if (!function_exists('validateSql')) {
    function validateSql($string, $limit = 100){
        $sqlstring = strtolower($string);
        $sqlstring = str_replace(';','',$sqlstring);

        if(strpos($sqlstring,'limit')===FALSE){
            $sqlstring = $sqlstring.' limit '.$limit;
        }
        return $sqlstring;
    }

}

if (!function_exists('getDbType')) {

    function getDbType($string){
        if($string=='temp_products'){
            return 'sql';
        }else{
            return 'mongo';
        }
    }

}