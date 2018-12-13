# Helpers

Helpers are files with independent functions which are used in models or controllers to simplify certain tasks.

# Extract Helper

Extract helper file contains function which are used mainly in `Extract_model`.

It contains following functions:

Function which recursively converts multidimensional array to one-dimensional array for ease of iteration

    if (!function_exists('toSingleArray')) {
        /**
         * 
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
    
Strip uri from redirects and any parameters
    
    if (!function_exists('getCleanUrl')) {
        /**
         * 
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
    
Function which returns substring located between two other substrings
    
    if (!function_exists('get_string_between')) {
        /**
         * 
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
    
Cuts and removes substring between two other substrings
    
    if (!function_exists('delete_all_between')) {
        /**
         * 
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
    
Function which returns time between two microtimes in seconds or minutes   
    
    if (!function_exists('getTime')) {
        /**
         *
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

# Transform Helper

Transform helper file contains function which are used mainly in `Transform_model`.

It contains following functions:

Function that converts boolean values to string values

    if (!function_exists('convertBoolean')) {
        /**
         * 
         * @param $attribute - input value, boolean or NULL
         * @return string - string "TRUE", "FALSE", or "NULL"
         */
        function convertBoolean($attribute){
            if($attribute==='NULL'){
                return 'NULL';
            }
            if($attribute === true){
                return "TRUE";
            }else{
                return "FALSE";
            }
        }
    }
    
Function which helps to retrieve value of an attribute and strips illegal characters in strings, if it is NULL it returns string "NULL"    
    
    if (!function_exists('getAttributeOrNull')) {
        /**
         * 
         * @param $attribute - name of an attribute
         * @return mixed|string - attribute or null
         */
        function getAttributeOrNull($attribute){
            $value =  isset($attribute) ? $attribute : 'NULL';
            //Add single quotes around values which are of a string type or an empty string
            if($value!=='NULL'&&is_string($value)){
                //replace single quotes with double quotes inside strings
                $value = str_replace('\'','"',$value);
                $value = '\''.$value.'\'';
    
            }
            return $value;
        }
    
    }
    
Function which helps to generate checkboxes for views
    
    if (!function_exists('generateCheckboxes')) {
        /**
         * 
         * @return array of checkboxes
         */
        function generateCheckboxes(){
        //value has to be like in mongo collection
            $checkboxes= [
                [
                'value'=>'offerTitle.title','id'=>'title_chb','label'=>'Title'
            ],[
                    'value'=>'price.priceInteger','id'=>'price_chb','label'=>'Price'
                ],[
                    'value'=>'offerTitle.sellerName','id'=>'seller_chb','label'=>'Seller'
                ],[
                    'value'=>'offerTitle.sellerListingUrl','id'=>'sellerlink_chb','label'=>'Seller link'
                ],[
                    'value'=>'coins.quantityWithLabel','id'=>'coins_chb','label'=>'Coins'
                ],[
                    'value'=>'notifyAndWatch.quantity','id'=>'amount_chb','label'=>'Amount'
                ],[
                    'value'=>'schema.description','id'=>'description_chb','label'=>'Description'
                ],[
                    'value'=>'offerTitle.superSellerActive','id'=>'superstatus_chb','label'=>'Super status'
                ],[
                    'value'=>'schema.itemCondition','id'=>'condition_chb','label'=>'Condition'
                ],[
                    'value'=>'biddingSection.endingDate','id'=>'enddate_chb','label'=>'End date'
                ],[
                    'value'=>'biddingSection.nextPrice','id'=>'nextprice_chb','label'=>'Next price'
                ],[
                    'value'=>'popularity.label','id'=>'popularity_chb','label'=>'Popularity'
                ],[
                    'value'=>'price.installments.quantity','id'=>'installments_chb','label'=>'Installments qty'
                ],[
                    'value'=>'price.installments.free','id'=>'insttype_chb','label'=>'Inst type'
                ],[
                    'value'=>'price.installments.price','id'=>'qtyprice_chb','label'=>'Inst price'
                ]
    
            ];
            return $checkboxes;
    
        }
    
    }

# CRUD Helper

CRUD helper file contains function which are used mainly in `Crud_model` and `Etl` controller.

It contains following functions:

Function which helps to validate sql string.

    if (!function_exists('validateSql')) {
        /**
         * 
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

Function which helps to determine database type in case that mongo and mysql has table\collection with same name
    
    if (!function_exists('getDbType')) {
    
        /**
         * 
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