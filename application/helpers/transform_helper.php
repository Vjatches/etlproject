<?php

if (!function_exists('generateCheckboxes')) {
    function generateCheckboxes(){

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
                'value'=>'installments.quantity','id'=>'installments_chb','label'=>'Installments qty'
            ],[
                'value'=>'installments.free','id'=>'insttype_chb','label'=>'Inst type'
            ],[
                'value'=>'installments.price','id'=>'qtyprice_chb','label'=>'Inst price'
            ]

        ];
        return $checkboxes;

    }

}