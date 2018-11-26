<?php

/**
 * Worm because it eats Apples
 * Class Worm
 * Crawler which is used to scrap PRODUCT pages at allegro (class CategoryCrawler is used to scrap category pages)
 */
class Worm
{
    /**
     * This crawler object
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;

    /**
     * Worm constructor.
     * Creates worm that scraps data from given itemURL
     * @param $itemURL
     */
    public function __construct($itemURL)
    {
        $this->crawler= new \Symfony\Component\DomCrawler\Crawler($itemURL,'https://allegro.pl/');
    }


    /**
     * Function that extracts data from allegro javascript and packs it to array
     * @return mixed - associative array with data
     */
    public function getJson(){
        //Extract <script> tag from allegro
        $text = $this->crawler->filter('[data-box-name="summary"]')->filter('script')->html();

        //Cut out json string from <script> tag, trim and process it so it will look like valid json
        $start = '"primarySlot":';
        $end = '"additionalServices"';
        $jsonstring = get_string_between($text,$start,$end);
        $stripped = str_replace($jsonstring, "{},",$text);

        //Further trim, process and decode html special character used for polish characters
        $json = html_entity_decode(rtrim(strstr($stripped,"{\""),";"));
        return json_decode($json,true);
    }

}
