<?php

if (!function_exists('generateCheckboxes')) {
    function generateCheckboxes(){

        $checkboxes= [
            [
            'value'=>'title','id'=>'title_chb','label'=>'Title'
        ],[
                'value'=>'price','id'=>'price_chb','label'=>'Price'
            ],[
                'value'=>'seller','id'=>'seller_chb','label'=>'Seller'
            ],[
                'value'=>'sellerlink','id'=>'sellerlink_chb','label'=>'Seller link'
            ],[
                'value'=>'coins','id'=>'coins_chb','label'=>'Coins'
            ],[
                'value'=>'amount','id'=>'amount_chb','label'=>'Amount'
            ],[
                'value'=>'description','id'=>'description_chb','label'=>'Description'
            ],[
                'value'=>'superstatus','id'=>'superstatus_chb','label'=>'Super status'
            ],[
                'value'=>'condition','id'=>'condition_chb','label'=>'Condition'
            ],[
                'value'=>'enddate','id'=>'enddate_chb','label'=>'End date'
            ],[
                'value'=>'nextprice','id'=>'nextprice_chb','label'=>'Next price'
            ],[
                'value'=>'popularity','id'=>'popularity_chb','label'=>'Popularity'
            ],[
                'value'=>'installments','id'=>'installments_chb','label'=>'Installments'
            ],[
                'value'=>'insttype','id'=>'insttype_chb','label'=>'Inst type'
            ],[
                'value'=>'qtypry','id'=>'qtyprice_chb','label'=>'Inst qty+price'
            ]

        ];
        return $checkboxes;

    }

}