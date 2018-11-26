<?php

if (!function_exists('validateSql')) {
    /**
     * Function which helps to validate sql string.
     * @param $string - input string
     * @param int $limit - limit to sql query
     * @return mixed|string - transformed string
     */
    function validateSql($string, $limit = 100){
        //Convert to lower case
        $sqlstring = strtolower($string);
        //Replace any occurrence of semicolon
        $sqlstring = str_replace(';','',$sqlstring);
        //If there was no limit specified add default limit
        if(strpos($sqlstring,'limit')===FALSE){
            $sqlstring = $sqlstring.' limit '.$limit;
        }
        return $sqlstring;
    }

}

if (!function_exists('getDbType')) {

    /**
     * Function which helps to determine database type in case that mongo and mysql has table\collection with same name
     * @param $string - name of data structure
     * @return string - type of database
     */
    function getDbType($string){
        if($string=='temp_products' || $string=='sql_products'){
            return 'sql';
        }else{
            //If name of data_structure is not one of reserved sql table names then it is mongo
            return 'mongo';
        }
    }

}