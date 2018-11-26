<?php
/**
 * Class AllegroCrawler
 * Parent Crawler class which is used to scrap data from Allegro
 */
class AllegroCrawler{
    /**
     * Object of type Symfony\Domcrawler\Crawler which hosts current crawler
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;

    /**
     * AllegroCrawler constructor.
     * @param $categoryURI - uri to be scrapped by crawler
     */
    public function __construct($categoryURI)
    {
        //Create new DOMDocument object which contains html not like string but like object
        $dom = new DOMDocument('1.0');
        //Load content from provided link to this object
        @$dom->loadHTMLFile($categoryURI);

        //Create crawler on this object and mark base url as allegro main url
        $this->crawler = new \Symfony\Component\DomCrawler\Crawler($dom, 'https://allegro.pl/');
    }
}