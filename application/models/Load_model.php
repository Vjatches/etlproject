<?php

/**
 * Class Load_model
 * Main model for the Load process. It handles all Load back-end logic
 */
class Load_model extends CI_Model{

    /**
     * Load_model constructor.
     * Here we connect to databases and load helpers
     */
    function __construct()
    {
        //connect to mysql, connection parameters are in application/config/database.php
        $this->load->database('mysql');
    }

    /**
     * Function which is used to get rows quantity from temp_products table
     * @return int - amount of rows
     */
    public function getRowsQuantity(){
        return $this->db->count_all('temp_products');
    }

    /**
     * Main function of Load process which uses other functions
     * @param $numrows - amount of rows to load from temp_products to products tables
     * @param $mod_id - identifier of a process which initiates load
     * @return array - report on load process
     */
    public function runLoad($numrows, $mod_id){
        //Load timer
        $this->load->library('timer');
        //start timer
        $this->timer->start();
        //Set imitial limit to unlimited
        $limit = '';
        //If user specified limit overwrite initial limit
        if($numrows!=0){
            $limit = 'limit '.$numrows;
        }
        //Prepare select query
        $selectsql = 'select * from temp_products '.$limit.';';
        //Select everything from temp_products
        $selectquery = $this->db->query($selectsql);
        //Start sql transaction. This query is used before loops to speed up sql inserts
        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);
        //Create counters for inserted, updated and not affected rows
        $count_insert =0;
        $count_update = 0;
        $count_notaffected = 0;

        //Loop through all records which we selected from `temp_products` and compare with records in 'products'
        foreach ($selectquery->result() as $row){
            //Set id of a process which is used to create records
            $create_id = $mod_id;
            //Prepare sql: Insert if there is no such record, update on duplicate key if there is such record with different data
            $loadsql = 'insert into products (`_id`, title, price, seller_name, seller_url, coins, available_quantity, description, super_status, item_condition, auction_ending_date, next_price, popularity_data, installments_quantity, free_installments, installments_price, create_id) VALUES (
                      \''.$row->_id.'\',
                      \''.$row->title.'\',
                      \''.$row->priceInteger.'\',
                      \''.$row->sellerName.'\',
                      \''.$row->sellerListingUrl.'\',
                     \''.$row->quantityWithLabel.'\',
                      \''.$row->quantity.'\',
                      \''.$row->description.'\',
                      \''.$row->superSellerActive.'\',
                      \''.$row->itemCondition.'\',
                      \''.$row->endingDate.'\',
                      \''.$row->nextPrice.'\',
                      \''.$row->label.'\',
                      \''.$row->installmentsquantity.'\',
                      \''.$row->installmentsfree.'\',
                      \''.$row->installmentsprice.'\',
                      \''.$create_id.'\') 
                      ON DUPLICATE KEY UPDATE modify_id = values(create_id), title = values(title),price = values(price),seller_name = values(seller_name),
                      seller_url = values(seller_url),coins = values(coins),available_quantity = values(available_quantity),description = values(description),super_status = values(super_status),
                      item_condition = values(item_condition),auction_ending_date = values(auction_ending_date),next_price = values(next_price),popularity_data = values(popularity_data),installments_quantity = values(installments_quantity),
                      installments_price = values(installments_price),free_installments = values(free_installments);';
            //Run previously prepared sql
            $this->db->query($loadsql);
            //Increase counters based on what happened with record
            if($this->db->affected_rows()==1){
                $count_insert += 1;
            }elseif($this->db->affected_rows()==2){
                $count_update += 1;
            }else{
                $count_notaffected += 1;
            }
        }
        //Commit transaction. This query is used after loop inserts to summarise transaction and speed it up
        $commitsql = 'commit';
        $this->db->query($commitsql);

        //Pack and return report data: counters and execution time
        return ['executiontime'=>$this->timer->stop(),'inserted' => $count_insert, 'updated' => $count_update, 'not_affected'=>$count_notaffected];
    }


}