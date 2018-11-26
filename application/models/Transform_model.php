<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 11/8/2018
 * Time: 12:42
 */

class Transform_model extends CI_Model
{
    private $mongoClient;
    function __construct()
    {
        $this->mongoClient= new \MongoDB\Client('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net/test?retryWrites=true');
        $this->load->database('mysql');
        $this->load->helper('url');
        $this->load->helper('transform');
    }

    public function setChoice($input, $consent){
        if($consent != 'default'){
            return;
        }
        $this->db->query('DELETE from choice');

        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);

        foreach ($input as $checkbox){
            $sql = 'insert into choice (checkbox) value ("'.$checkbox.'");';
            $this->db->query($sql);
        }

        $commitsql = 'commit';
        $this->db->query($commitsql);

    }

    public function getChoice(){
        $sql = 'select * from choice';
        $query = $this->db->query($sql);
        $result = [];
        foreach ($query->result() as $row){
            $result[] = $row->checkbox;
        }
        return $result;
    }

    public function runTransform($input){
        set_time_limit(0);
        $this->load->library('timer');
        $this->timer->start();

        $result['mongodb'] = $this->aggregateData($input);
        $result['mysql'] = $this->transformToSql();

        $result['executiontime']=$this->timer->stop();
        return $result;
    }

    public function aggregateData(array $arrayOfValues){

        $extracted = $this->mongoClient->extracthub->products;
        $options = $this->generateAggregateOptions($arrayOfValues);
        $aggregated = $this->mongoClient->extracthub->aggregated;

        $aggregated->deleteMany([]);
        $result = $extracted->aggregate($options);
        return $result;
    }

    public function generateAggregateOptions(array $arrayOfValues){
        $project = ["id"=>1];
        foreach ($arrayOfValues as $attribute){
            $explodedAttribute = explode('.', $attribute);
            $amount = count($explodedAttribute);
            if($amount>2){
                $project[$explodedAttribute[$amount-2].''.$explodedAttribute[$amount-1]] = '$'.$attribute;
            }else{
                $project[$explodedAttribute[1]] = '$'.$attribute;
            }

        }
        $ops = array(
            array(
                '$project' => $project
            ),
            array(
                '$out' => "aggregated"
            )
        );
        return $ops;
    }

    public function transformToSql(){
        $this->db->reconnect();
        $mongodb = $this->mongoClient->extracthub->aggregated;
        $cursor = $mongodb->find();
        $failed =[];

        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);

        foreach ($cursor as $product){

            $id= isset($product['_id']) ? $product['_id'] : 'NULL';
            $title= isset($product['title']) ? $product['title'] : 'NULL';
            $priceInteger= isset($product['priceInteger']) ? $product['priceInteger'] : 'NULL';
            $sellerName= isset($product['sellerName']) ? $product['sellerName'] : 'NULL';
            $sellerListingUrl= isset($product['sellerListingUrl']) ? $product['sellerListingUrl'] : 'NULL';
            $quantityWithLabel= isset($product['quantityWithLabel']) ? $product['quantityWithLabel'] : 'NULL';
            $quantity= isset($product['quantity']) ? $product['quantity'] : 'NULL';
            $description= isset($product['description']) ? $product['description'] : 'NULL';
            $superSellerActive= isset($product['superSellerActive']) ? $product['superSellerActive'] : 'NULL';
            $itemCondition= isset($product['itemCondition']) ? $product['itemCondition'] : 'NULL';
            $endingDate= isset($product['endingDate']) ? $product['endingDate'] : 'NULL';
            $endingDate = getAttributeOrNull($endingDate);
            $nextPrice= isset($product['nextPrice']) ? $product['nextPrice'] : 'NULL';
            $label= isset($product['label']) ? $product['label'] : 'NULL';
            $installmentsquantity= isset($product['installmentsquantity']) ? $product['installmentsquantity'] : 'NULL';
            $installmentsfree= isset($product['installmentsfree']) ? $product['installmentsfree'] : 'NULL';
            $installmentsprice= isset($product['installmentsprice']) ? $product['installmentsprice'] : 'NULL';



            $sql = 'insert into temp_products VALUES (
                      '.getAttributeOrNull($id).',
                      '.getAttributeOrNull($title).',
                      '.trim(str_replace(' ','',getAttributeOrNull($priceInteger)), '\'').',
                      '.getAttributeOrNull($sellerName).',
                      '.getAttributeOrNull($sellerListingUrl).',
                     '.getAttributeOrNull($quantityWithLabel).',
                      '.getAttributeOrNull($quantity).',
                      '.getAttributeOrNull($description).',
                      '.convertBoolean($superSellerActive).',
                      '.str_replace('Condition', '',str_replace('http://schema.org/','',getAttributeOrNull($itemCondition))).',
                      '.$endingDate.',
                      '.getAttributeOrNull($nextPrice).',
                      '.getAttributeOrNull($label).',
                      '.getAttributeOrNull($installmentsquantity).',
                      '.convertBoolean($installmentsfree).',
                      '.getAttributeOrNull($installmentsprice).') 
                      ON DUPLICATE KEY UPDATE title = values(title),priceInteger = values(priceInteger),sellerName = values(sellerName),
                      sellerListingUrl = values(sellerListingUrl),quantityWithLabel = values(quantityWithLabel),quantity = values(quantity),description = values(description),superSellerActive = values(superSellerActive),
                      itemCondition = values(itemCondition),endingDate = values(endingDate),nextPrice = values(nextPrice),label = values(label),installmentsquantity = values(installmentsquantity),
                      installmentsprice = values(installmentsprice),installmentsfree = values(installmentsfree);';

            if(!$this->db->query($sql)){
                $failed[] = $product['_id'];
            }
        }

        $commitsql = 'commit';
        $this->db->query($commitsql);
        $result['failed']=$failed;
        $result['numrows']=$this->db->count_all('temp_products');


        return $result;
    }
}