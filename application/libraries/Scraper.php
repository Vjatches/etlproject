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


    public function scrape(array $urls = [], $concurrencyLimit)
    {
        $queue = new Clue\React\Mq\Queue($concurrencyLimit, null, function ($url) {
            return $this->client->get($url);
        });
        $this->scraped = [];

        foreach ($urls as $url) {
           $queue($url)->then(
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

    public function getData()
    {
        return $this->scraped;
    }
}