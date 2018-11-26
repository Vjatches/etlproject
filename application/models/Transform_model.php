<?php

/**
 * Class Transform_model
 * Main model for the Transform process. It handles all Transform back-end logic
 */
class Transform_model extends CI_Model
{
    /**
     * Variable which holds object responsible for connection with MongoDB
     * @var \MongoDB\Client
     */
    private $mongoClient;

    /**
     * Transform_model constructor.
     * Here we connect to databases and load helpers
     */
    function __construct()
    {
        //connect to MongoDB
        $this->mongoClient= new \MongoDB\Client('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net/test?retryWrites=true');
        //connect to mysql, connection parameters are in application/config/database.php
        $this->load->database('mysql');
        //Load helpers
        $this->load->helper('url');
        $this->load->helper('transform');
    }

    /**
     * Function which is used to save information which checkboxes from transform page has to remain checked when we reload page
     * @param $input - array of checkboxes
     * @param $consent - do we want to set array of checkboxes as default or no
     */
    public function setChoice($input, $consent){
        //check if we really want to sent it as default
        if($consent != 'default'){
            //if not do nothing and exit function
            return;
        }
        //clear previous default set of checkboxes which are stored in database
        $this->db->query('DELETE from choice');
        //Start sql transaction. This query is used before loops to speed up sql inserts
        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);
        //Insert information with names of checkboxes which we want to remain checked at later loads
        foreach ($input as $checkbox){
            $sql = 'insert into choice (checkbox) value ("'.$checkbox.'");';
            $this->db->query($sql);
        }
        //Commit transaction. This query is used after loop inserts to summarise transaction and speed it up
        $commitsql = 'commit';
        $this->db->query($commitsql);
    }

    /**
     * Function which is used to retrieve information from database about which checkboxes should be checked on page load.
     * It loads default set of checkboxes
     * @return array - array of checkboxes which have to be checked
     */
    public function getChoice(){
        //Select everything from choice table
        $sql = 'select * from choice';
        $query = $this->db->query($sql);
        $result = [];
        //Write names of checkboxes to result array and return int
        foreach ($query->result() as $row){
            $result[] = $row->checkbox;
        }
        return $result;
    }

    /**
     * Main function of Transform process which uses other functions
     * @param $input - set of checkboxes which show what data do we want to transform
     * @return mixed - report with data about transform process
     */
    public function runTransform($input){
        //Set php time limit to 0 so it wont timeout during long process of quering database
        set_time_limit(0);
        //Load timer library
        $this->load->library('timer');
        //Start time
        $this->timer->start();
        //Aggregate data from mongoDB and write aggregation report to $result['mongodb']
        $result['mongodb'] = $this->aggregateData($input);
        //Transform data from aggregated mongodb collection to temp_products sql table and write report to variable $result['mysql']
        $result['mysql'] = $this->transformToSql();
        //stop timer and write execution time to $result['executiontime'] variable
        $result['executiontime']=$this->timer->stop();
        return $result;
    }

    /**
     * Function which extracts only useful data from big mongoDB object and aggregates it to simple key=>value pair
     * @param array $arrayOfValues - set of checkboxes which show what data do we want to aggregate
     * @return Traversable - object which contains report about aggregation process
     */
    public function aggregateData(array $arrayOfValues){
        //Assign mongodb collection 'products' to a variable
        $extracted = $this->mongoClient->extracthub->products;
        //Generate aggregation options based on array of chosen values
        $options = $this->generateAggregateOptions($arrayOfValues);
        //Assign mongodb collection 'aggregated' to a variable
        $aggregated = $this->mongoClient->extracthub->aggregated;
        //Clear 'aggregated' collection from any previous data
        $aggregated->deleteMany([]);
        //Use aggregation pipeline framework with aggregation options on collection with big objects
        $result = $extracted->aggregate($options);
        return $result;
    }

    /**
     * Function that sets and generates aggregation options
     * @param array $arrayOfValues - set of checkboxes which show what data do we want to aggregate
     * @return array - array of options to pass to aggregation pipeline
     */
    public function generateAggregateOptions(array $arrayOfValues){
        //Set what attributes do we want to project during aggregation
        //project id always
        $project = ["id"=>1];
        //Foreach attribute project it as its final name and reduce nesting to single level
        foreach ($arrayOfValues as $attribute){
            //Determine how deeply is attribute nested by exploding it by dot
            $explodedAttribute = explode('.', $attribute);
            //Amount of product elements = depth of nesting
            $amount = count($explodedAttribute);
            //If attribute is nested reduce nesting (e.g. price.installments.installmentsQuantity becomes just installmentsQuantity)
            if($amount>2){
                $project[$explodedAttribute[$amount-2].''.$explodedAttribute[$amount-1]] = '$'.$attribute;
            }else{
                $project[$explodedAttribute[1]] = '$'.$attribute;
            }

        }
        //Create aggregation pipeline
        $ops = array(
            array(
                '$project' => $project //array of options which configure how to project attributes
            ),
            array(
                '$out' => "aggregated" //output of aggregation write directly to `aggregated` collection
            )
        );
        //Return aggregation pipeline
        return $ops;
    }

    /**
     * Function that migrates data from mongoDB to sql table
     * @return mixed - report on migration process
     */
    public function transformToSql(){
        //Refresh connection to sql database
        $this->db->reconnect();
        //Assign mongoDB collection to variable
        $mongodb = $this->mongoClient->extracthub->aggregated;
        //Find everything in this collection and assign to cursor object
        $cursor = $mongodb->find();
        //Container which will host failed inserts
        $failed =[];
        //Start sql transaction. This query is used before loops to speed up sql inserts
        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);

        //Loop through all products in MongoDB cursor
        foreach ($cursor as $product){
            //If cursor doesn't have this value assign string "NULL" to it
            //During assignment convert boolean values to respective strings using helper methods
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


            //Perform INSERT .... ON DUPLICATE KEY UPDATE .... - insert value, if value with such primary key exists, update certain fields
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

            //If something went wrong add id of failed insert to $failed array
            if(!$this->db->query($sql)){
                $failed[] = $product['_id'];
            }
        }
        //Commit transaction. This query is used after loop inserts to summarise transaction and speed it up
        $commitsql = 'commit';
        $this->db->query($commitsql);
        //Pack data with errors and amount of rows affected to array and return it
        $result['failed']=$failed;
        $result['numrows']=$this->db->count_all('temp_products');
        return $result;
    }
}