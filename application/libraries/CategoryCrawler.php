<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 10/15/2018
 * Time: 12:01
 */
require_once 'AllegroCrawler.php';
class CategoryCrawler extends AllegroCrawler
{
    protected $crawler;
    public function __construct($categoryURI = ALLEGRO_CATEGORY_URL)
    {
        parent::__construct($categoryURI);
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
            $url=getCleanUrl($node->link()->getUri());
            return $url;
        });
        return $linksArray;
    }

}
