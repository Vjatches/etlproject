<?php

if (!function_exists('convertBoolean')) {
    /**
     * Function that converts boolean values to string values
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
if (!function_exists('getAttributeOrNull')) {
    /**
     * Function which helps to retrieve value of an attribute and strips illegal characters in strings, if it is NULL it returns string "NULL"
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
if (!function_exists('generateCheckboxes')) {
    /**
     * Function which helps to generate checkboxes for views
     * @return array
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
