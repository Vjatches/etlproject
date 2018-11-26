<?php
require_once 'AllegroCrawler.php';

/**
 * Class CategoryCrawler
 * Crawler which is used to scrap CATEGORY pages at allegro (class Worm is used to scrap product pages)
 */
class CategoryCrawler extends AllegroCrawler
{
    /**
     * This crawler object
     * @var
     */
    protected $crawler;

    /**
     * CategoryCrawler constructor.
     * @param array $category_uri_array - category link from which crawler has to scrap content, has to be an array due to framework limitations
     */
    public function __construct(array $category_uri_array)
    {
        //get first (and only) item of an array
       $url =  $category_uri_array[0];
       //Invoke parent constructor and create crawler on this link
        parent::__construct($url);
    }

    /**
     * Function which returns amount of pages from current category
     * @return mixed
     */
    function getAmountOfPages()
    {
        //Get value from html element identified by [class="m-pagination__text"] which hosts value of max pages
        $numberOfPages = $this->crawler->filter('[class="m-pagination__text"]')->text();
        return $numberOfPages;
    }


    /**
     * Function that collects links to products from current category page
     * @return mixed - array of links
     */
    function getProductLinksFromPage()
    {
        //Extract Big div with items from page
        $div = $this->crawler->filter('[data-box-name="items container"]');
        //Extract Headers from big div which contain links to items
        $h2 = $div->filter('[class="_4462670  "],[class="_4462670 _7b0067f "]');
        //Extract Links from inside headers
        $anchors = $h2->filter('a');
        //Extract final uri's from links and push them to $linksArray
        $linksArray = $anchors->each(function (\Symfony\Component\DomCrawler\Crawler $node, $i) {
            $url=getCleanUrl($node->link()->getUri());
            return $url;
        });
        return $linksArray;
    }

}
