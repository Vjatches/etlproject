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
        $this->crawler= new \Symfony\Component\DomCrawler\Crawler($itemURL);
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
        if( $this->crawler->filter('[value="licytuj"]')->count()){
            return true;
        }else{
            return false;
        }
    }

}