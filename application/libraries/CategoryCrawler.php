<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 10/15/2018
 * Time: 12:01
 */

class CategoryCrawler
{
    private $crawler;

    public function __construct($categoryURI = 'https://allegro.pl/kategoria/laptopy-apple-77915')
    {
        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($categoryURI);
        $this->crawler = new \Symfony\Component\DomCrawler\Crawler($dom, 'https://allegro.pl/');
    }

    function getAmountOfPages()
    {
        $numberOfPages = $this->crawler->filter('[class="m-pagination__text"]')->text();
        return $numberOfPages;
    }

    function getProductLinksFromPage()
    {
        $div = $this->crawler->filter('[data-box-name="items container"]');
        $h2 = $div->filter('[class="_4462670  "],[class="_4462670 _7b0067f "]');
        $anchors = $h2->filter('a');
        $linksArray = $anchors->each(function (\Symfony\Component\DomCrawler\Crawler $node, $i) {
            return $node->link()->getUri();
        });
        return $linksArray;
    }


}