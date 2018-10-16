<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 10/16/2018
 * Time: 10:08
 */

class AllegroCrawler{
    protected $crawler;
    public function __construct($categoryURI)
    {
        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($categoryURI);
        $this->crawler = new \Symfony\Component\DomCrawler\Crawler($dom, 'https://allegro.pl/');
    }
}