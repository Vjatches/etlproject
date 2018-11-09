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
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('transform');
    }

    public function runTransform($input){

        $this->aggregateData($input);

        return $input;
    }

    public function aggregateData(array $arrayOfValues){

        $extracted = $this->mongoClient->extracthub->products;
        $options = $this->generateAggregateOptions($arrayOfValues);
        $aggregated = $this->mongoClient->extracthub->aggregated;
        $aggregated->deleteMany([]);
        $extracted->aggregate($options);
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
}