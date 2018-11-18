<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 11/12/2018
 * Time: 15:31
 */

class Load_model extends CI_Model{

    function __construct()
    {
        $this->load->helper('url');
        $this->load->database('mysql');
    }

    public function getRowsQuantity(){
        return $this->db->count_all('temp_products');
    }

    public function runLoad($numrows, $mod_id){
        $this->load->library('timer');
        $this->timer->start();
        $limit = '';
        if($numrows!=0){
            $limit = 'limit '.$numrows;
        }
        $selectsql = 'select * from temp_products '.$limit.';';
        $selectquery = $this->db->query($selectsql);

        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);
        $count_insert =0;
        $count_update = 0;
        $count_notaffected = 0;


        foreach ($selectquery->result() as $row){

            $create_id = $mod_id;

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
            $this->db->query($loadsql);
            if($this->db->affected_rows()==1){
                $count_insert += 1;
            }elseif($this->db->affected_rows()==2){
                $count_update += 1;
            }else{
                $count_notaffected += 1;
            }
        }

        $commitsql = 'commit';
        $this->db->query($commitsql);


        return ['executiontime'=>$this->timer->stop(),'inserted' => $count_insert, 'updated' => $count_update, 'not_affected'=>$count_notaffected];
    }


}