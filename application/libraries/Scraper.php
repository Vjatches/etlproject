<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 10/16/2018
 * Time: 22:58
 */
use Clue\React\Buzz\Browser;
require_once 'Worm.php';
class Scraper
{
    private $client;
    private $scraped = [];

    public function setClient(Browser $client){
        $this->client = $client;
    }

    public function scrape(array $urls = [])
    {
        $this->scraped = [];

        foreach ($urls as $url) {
            $this->client->get($url)->then(
                function (\Psr\Http\Message\ResponseInterface $response) {
                    $this->scraped[] = $this->extractFromHtml((string) $response->getBody());
                });
        }
    }

    public function extractFromHtml($html)
    {
        $crawler = new Worm($html);

        $title = $crawler->getAttribute(getClassSelector('title'));
        $price = $crawler->getAttribute(getClassSelector('price'));
        $seller = $crawler->getAttribute(getClassSelector('seller'));
        return [
            'title'        => $title,
            'price'       => $price,
            'seller'  => $seller,
        ];
    }

    public function getLinksFromPages(array $urls = [])
    {
        $this->scraped = [];

        foreach ($urls as $url) {
            $this->client->get($url)->then(
                function (\Psr\Http\Message\ResponseInterface $response) {
                    $this->scraped[] = $this->extractFromHtml((string) $response->getBody());
                });
        }
    }
    public function extractLinks($html)
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);

        $div = $crawler->filter('[data-box-name="items container"]');
        $h2 = $div->filter('[class="_4462670  "],[class="_4462670 _7b0067f "]');
        $anchors = $h2->filter('a');
        $linksArray = $anchors->each(function (\Symfony\Component\DomCrawler\Crawler $node, $i) {
            $url=getCleanUrl($node->link()->getUri());
            return $url;
        });
        return $linksArray;
    }

    public function getData()
    {
        return $this->scraped;
    }
}