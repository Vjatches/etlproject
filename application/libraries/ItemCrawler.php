<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 10/16/2018
 * Time: 10:09
 */
require_once 'AllegroCrawler.php';
class ItemCrawler extends AllegroCrawler
{
    protected $crawler;
    private $url;
    public function __construct($categoryURI = 'https://allegro.pl/kategoria/laptopy-apple-77915')
    {
        parent::__construct($categoryURI);
        $this->url=$categoryURI;
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