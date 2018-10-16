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

    public function getPrice(){
        try{
        $price = strip_tags($this->crawler->filter('[class="_1f306df3 _1c943cca d7cfa755"]')->html(),'span');
        }catch(InvalidArgumentException $exception){
            $price = strip_tags($this->crawler->filter('[class="m-price m-price--primary"]')->html(),'span');
            $price = $price.' licytacja';
        }
        finally{
            return $price;
        }

    }
    public function getTitle(){
        try{
            $title = strip_tags($this->crawler->filter('[class="_884d145b"]')->html(),'span');
        }catch(InvalidArgumentException $exception){
            $title = strip_tags($this->crawler->filter('[class="m-heading m-heading--xs si-title"]')->html(),'span');
            $title = $title.' licytacja';
        }
        finally{
            return $title;
        }
    }
    public function getSeller(){
        try{
            $seller = strip_tags($this->crawler->filter('[class="_28bad9f5 e42e4878 _808f2003"]')->html(),'span');
        }catch(InvalidArgumentException $exception){
            $seller = strip_tags($this->crawler->filter('[class="m-link"]')->html(),'span');
            $seller = $seller.' licytacja';
        }
        finally{
            return $seller;
        }
    }

}