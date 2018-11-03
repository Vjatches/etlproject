<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 10/16/2018
 * Time: 23:07
 */

//Worm because it eats Apples
class Worm
{
    protected $crawler;

    public function __construct($itemURL)
    {
        $this->crawler= new \Symfony\Component\DomCrawler\Crawler($itemURL,'https://allegro.pl/');
    }

    //get html of desired attribute, pass attribute class selector returned by extract_helper
    //getClassSelector method
    public function getAttribute(array $classSelector){
        if(!$this->ifAuction()){
            $attribute = strip_tags($this->crawler->filter($classSelector['regular'])->html(),'span');
        }else{
            $attribute = strip_tags($this->crawler->filter($classSelector['auction'])->html(),'span');
        }
        return $attribute;
    }
    //check if item page is an auction by looking for "licytuj" button
    public function ifAuction(){
        if( $this->crawler->filter('[data-analytics-interaction-custom-flow="PurchasingProcess"]')->count()){
            return true;
        }else{
            return false;
        }
    }

    public function getJson(){

        $text = $this->crawler->filter('[data-box-name="summary"]')->filter('script')->html();
        $start = '"primarySlot":';
        $end = '"additionalServices"';
        $jsonstring = get_string_between($text,$start,$end);
        $stripped = str_replace($jsonstring, "{},",$text);

        $json = rtrim(strstr($stripped,"{\""),";");
        return json_decode($json,true);
    }

}
