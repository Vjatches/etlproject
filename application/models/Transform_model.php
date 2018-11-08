<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 11/8/2018
 * Time: 12:42
 */

class Transform_model extends CI_Model
{
    function __construct()
    {
        $this->load->helper('url');
        $this->load->helper('transform');
    }

    public function runTransform($input){

        $client = new MongoDB\Client(
            'mongodb+srv://root:root@kreslav-hcr9i.mongodb.net/test?retryWrites=true');
        $extracted = $client->extracthub->products;
        $options = $this->generateAggregateOptions($input);
        $aggregated = $client->extracthub->aggregated;
        $aggregated->deleteMany([]);
        $extracted->aggregate($options);

        return $input;
    }

    public function generateAggregateOptions(array $input){

        $project = ["id"=>1];
        foreach ($input as $attribute){
            $explodedAttribute = explode('.', $attribute);
            $project[$explodedAttribute[1]] = $attribute;

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